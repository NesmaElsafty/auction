<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuctionService
{
    public function index($data)
    {
        $query = Auction::with(['category', 'user']);

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if (isset($data['category_id']) && $data['category_id']) {
            $query->where('category_id', $data['category_id']);
        }

        // Filter by status
        if (isset($data['status']) && $data['status']) {
            $query->where('status', $data['status']);
        }

        // Filter by type
        if (isset($data['type']) && $data['type']) {
            $query->where('type', $data['type']);
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
        DB::beginTransaction();
        try {
            // Determine user_id and user_type
            $userId = $user->id;
            $userType = User::class;

            // If agency_id is provided, verify it belongs to the user
            if (isset($data['agency_id']) && $data['agency_id']) {
                $agency = Agency::where('id', $data['agency_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (!$agency) {
                    throw new \Exception('Agency not found or does not belong to you');
                }

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
            ]);

            DB::commit();
            return $auction->load(['category', 'user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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

    /**
     * Verify that the user owns the auction (either directly or through an agency)
     */
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
}

