<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\ExportHelper;

class AuctionService
{
    public function index($data)
    {
        $query = Auction::with(['category', 'user']);
        $query->where('post_type', $data['post_type']);

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by category
        if (isset($data['category_id']) && $data['category_id'] !== 'all') {
            $query->where('category_id', $data['category_id']);
        }

        // Filter by status
        if (isset($data['status']) && $data['status'] !== 'all') {
            switch ($data['status']) {
                case 'pending':
                    // where now > start_date
                    $query->where('start_date', '>', now());
                    break;
                case 'current':
                    // where now > start_date and now < end_date
                    $query->where('start_date', '<', now())->where('end_date', '>', now());
                    break;
                case 'completed':
                    // where end_date < now
                    $query->where('end_date', '<', now());
                    break;
            }
        }

        // Filter by type
        if (isset($data['type']) && $data['type'] !== 'all') {
            $query->where('type', $data['type']);
        }

        // sorted by
        if (isset($data['sorted_by']) && $data['sorted_by'] !== 'all') {
            switch ($data['sorted_by']) {
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Filter by user (for user's own auctions)
        if (isset($data['user_id']) && $data['user_id']) {
            $query->where(function ($q) use ($data) {
                // Auctions where user is directly the owner
                $q->where(function ($subQ) use ($data) {
                    $subQ->where('user_id', $data['user_id'])
                         ->where('user_type', User::class);
                })
                // Or auctions where user owns the agency
                ->orWhere(function ($subQ) use ($data) {
                    $subQ->where('user_type', Agency::class)
                         ->whereIn('user_id', function ($query) use ($data) {
                             $query->select('id')
                                   ->from('agencies')
                                   ->where('user_id', $data['user_id']);
                         });
                });
            });
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $auction = Auction::with(['category', 'user', 'itemData.input'])->find($id);
        if (!$auction) {
            throw new \Exception('Auction not found');
        }
        return $auction;
    }

    public function store($data, User $user)
    {
            // Determine user_id and user_type
            $userId = $user->id;
            $userType = User::class;

            // If agency_id is provided, verify it belongs to the user
            if (isset($data['agency_id']) && $data['agency_id']) {
                $agency = Agency::where('id', $data['agency_id'])
                    ->where('user_id', $user->id)
                    ->first();

                $userId = $agency->id;
                $userType = Agency::class;
            }

            $auction = Auction::create([
                'category_id' => $data['category_id'],
                'user_id' => $userId,
                'user_type' => $userType,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? null,
                'is_infaz' => $data['is_infaz'] ?? false,
                'start_price' => $data['start_price'] ?? null,
                'end_price' => $data['end_price'] ?? null,
                'deposit_price' => $data['deposit_price'] ?? null,
                'minimum_bid_increment' => $data['minimum_bid_increment'] ?? null,
                'youtube_link' => $data['youtube_link'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'awarding_period_days' => $data['awarding_period_days'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'is_active' => $data['is_active'] ?? true,
                'is_approved' => $data['is_approved'] ?? false,
                'location' => $data['location'] ?? null,
                'lat' => $data['lat'] ?? null,
                'long' => $data['long'] ?? null,
                'viewing_date' => $data['viewing_date'] ?? null,
            ]);

        return $auction->load(['category', 'user']);
    }

    public function update($id, $data, User $user)
    {
        DB::beginTransaction();
        try {
            $auction = Auction::find($id);
            if (!$auction) {
                throw new \Exception('Auction not found');
            }

            // Verify ownership
            $this->verifyOwnership($auction, $user);

            // If agency_id is being changed, verify it belongs to the user
            if (isset($data['agency_id']) && $data['agency_id']) {
                $agency = Agency::where('id', $data['agency_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (!$agency) {
                    throw new \Exception('Agency not found or does not belong to you');
                }

                $data['user_id'] = $agency->id;
                $data['user_type'] = Agency::class;
            } elseif (isset($data['agency_id']) && $data['agency_id'] === null) {
                // If agency_id is null, set to user
                $data['user_id'] = $user->id;
                $data['user_type'] = User::class;
            }

            $auction->update($data);

            DB::commit();
            return $auction->load(['category', 'user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id, User $user)
    {
        DB::beginTransaction();
        try {
            $auction = Auction::find($id);
            if (!$auction) {
                throw new \Exception('Auction not found');
            }

            // Verify ownership
            $this->verifyOwnership($auction, $user);

            $auction->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function verifyOwnership($auction, $user)
    {
        $isOwner = false;

        // Check if auction belongs directly to user
        if ($auction->user_type === User::class && $auction->user_id === $user->id) {
            $isOwner = true;
        }

        // Check if auction belongs to user's agency
        if ($auction->user_type === Agency::class) {
            $agency = Agency::find($auction->user_id);
            if ($agency && $agency->user_id === $user->id) {
                $isOwner = true;
            }
        }

        if (!$isOwner) {
            throw new \Exception('You do not have permission to access this auction');
        }
        return $isOwner;
    }

    public function stats(): array
    {
        $now = now();
        $thirtyDaysAgo = now()->subDays(30);

        // Total Auctions (post_type = 'auction')
        // Current: all auctions until now
        $totalAuctionsNow = Auction::where('post_type', 'auction')->count();
        // Before: all auctions until 30 days ago
        $totalAuctionsBefore = Auction::where('post_type', 'auction')
            ->where('created_at', '<=', $thirtyDaysAgo)
            ->count();
        $totalAuctionsPercentage = $this->calculatePercentage($totalAuctionsBefore, $totalAuctionsNow);

        // Total Electronic Auctions (post_type = 'auction' AND type = 'online')
        $totalElectronicNow = Auction::where('post_type', 'auction')
            ->where('type', 'online')
            ->count();
        $totalElectronicBefore = Auction::where('post_type', 'auction')
            ->where('type', 'online')
            ->where('created_at', '<=', $thirtyDaysAgo)
            ->count();
        $totalElectronicPercentage = $this->calculatePercentage($totalElectronicBefore, $totalElectronicNow);

        // Total Hybrid Auctions (post_type = 'auction' AND type = 'both')
        $totalHybridNow = Auction::where('post_type', 'auction')
            ->where('type', 'both')
            ->count();
        $totalHybridBefore = Auction::where('post_type', 'auction')
            ->where('type', 'both')
            ->where('created_at', '<=', $thirtyDaysAgo)
            ->count();
        $totalHybridPercentage = $this->calculatePercentage($totalHybridBefore, $totalHybridNow);

        return [
            'total_auctions' => [
                'title' => 'إجمالي المزادات',
                'total' => $totalAuctionsNow,
                'percentage' => abs($totalAuctionsPercentage),
                'direction' => $this->getDirection($totalAuctionsPercentage),
            ],
            'total_electronic_auctions' => [
                'title' => 'إجمالي المزادات الإلكترونية',
                'total' => $totalElectronicNow,
                'percentage' => abs($totalElectronicPercentage),
                'direction' => $this->getDirection($totalElectronicPercentage),
            ],
            'total_hybrid_auctions' => [
                'title' => 'إجمالي مزادات الهجين',
                'total' => $totalHybridNow,
                'percentage' => abs($totalHybridPercentage),
                'direction' => $this->getDirection($totalHybridPercentage),
            ],
        ];
    }

    private function calculatePercentage(float $oldValue, float $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        $change = $newValue - $oldValue;
        $percentage = ($change / $oldValue) * 100;

        return round($percentage, 2);
    }

    private function getDirection(float $percentage): string
    {
        if ($percentage > 0) {
            return 'up';
        } elseif ($percentage < 0) {
            return 'down';
        } else {
            return 'neutral';
        }
    }

    // export auctions
    public function exportAuctions($data)
    {
        // export filtered auctions
        $data = array_merge($data, ['post_type' => 'auction']);
        $auctions = $this->index($data)->get();
        $csvData = [];
        foreach($auctions as $auction) {
            $auctionStatus = null;
            if($auction->start_date > now()) {
                $auctionStatus = 'Pending';
            } elseif($auction->start_date < now() && $auction->end_date > now()) {
                $auctionStatus = 'Current';
            } elseif($auction->end_date < now()) {
                $auctionStatus = 'Completed';
            }
            
            $csvData[] = [
                'اسم الاعلان' => $auction->name,
                'فئة الاعلان' => $auction->category->name,
                'وصف الاعلان' => $auction->description,
                'تاريخ البدء' => $auction->start_date,
                'تاريخ الانتهاء' => $auction->end_date,
                'الحاله' => $auctionStatus,
                'اسم المعلن' => $auction->user->name,
                'نوع المعلن' => $auction->user_type == User::class ? 'مستخدم' : 'وكالة',
                'نوع الاعلان' => $auction->type == 'online' ? 'إلكتروني' : 'هجين',
                'سعر العربون' => $auction->deposit_price,
                'هل هو اعلان علني' => $auction->is_infaz == 1 ? 'نعم' : 'لا',
                'سعر الانتهاء' => $auction->end_price == null ? $auction->start_price : $auction->end_price,
                'تاريخ الانشاء' => $auction->created_at,
                
            ];
        }
        $media = ExportHelper::exportToMedia($csvData, auth()->user(), 'exports', 'auctions.csv');
        return $media ?? null;
    }
}

