<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ExportHelper;
use App\Helpers\PaginationHelper;

class UserService
{
    public function index($data)
    {
        $query = User::query();

        // Apply filters
        if (isset($data['type']) && $data['type']) {
            $query->where('type', $data['type']);
        }

        if (isset($data['is_active']) && $data['is_active'] !== 'all') {
            $query->where('is_active', $data['is_active']);
        }

        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

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
        }

        return $query;
    }

    public function show($id)
    {
        $user = User::find($id);
        if(!$user){
            throw new \Exception('User not found');
        }
        return $user;
    }

    public function store($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'national_id' => $data['national_id'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'summary' => $data['summary'] ?? null,
            'link' => $data['link'] ?? null,
            'type' => $data['type'],
            'is_active' => $data['is_active'] ?? true,
            'password' => Hash::make($data['password']),
            // Bank data
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_name' => $data['bank_account_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'bank_address' => $data['bank_address'] ?? null,
            'IBAN' => $data['IBAN'] ?? null,
            'SWIFT' => $data['SWIFT'] ?? null,
        ]);
        return $user;
    }

    public function update($id, $data)
    {
        
        $user = User::find($id);
        if(!$user){
            throw new \Exception('User not found');
        }
        
        $user->update($data);
        
        return $user;
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user){
            throw new \Exception('User not found');
        }
        $user->forceDelete();
        return $user;
    }

    public function stats()
    {
        $now = now();
        $thirtyDaysAgo = now()->subDays(30);

        // Helper function to calculate percentage change
        $calculatePercentage = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100 : 0;
            }
            return round((($current - $previous) / $previous) * 100, 2);
        };

        // 1. الحسابات المحظورة (Blocked Accounts) - deleted_at != null AND type == 'user'
        $blockedCurrent = User::withTrashed()
            ->where('type', 'user')
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $now)
            ->count();

        $blockedPrevious = User::withTrashed()
            ->where('type', 'user')
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $thirtyDaysAgo)
            ->count();

        $blockedPercentage = $calculatePercentage($blockedCurrent, $blockedPrevious);
        $blockedIsIncrease = $blockedCurrent > $blockedPrevious;

        // 2. الحسابات النشطة (Active Accounts) - type == 'user' AND is_active == true (not deleted)
        $activeCurrent = User::where('type', 'user')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->count();

        // Count users that were active 30 days ago (created before 30 days ago, not deleted at that time, and currently active)
        // Note: This is an approximation since we don't track historical is_active changes
        $activePrevious = User::where('type', 'user')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->where('created_at', '<=', $thirtyDaysAgo)
            ->count();

        $activePercentage = $calculatePercentage($activeCurrent, $activePrevious);
        $activeIsIncrease = $activeCurrent > $activePrevious;

        // 3. إجمالي المستخدمين الأفراد (Total Individual Users) - type == 'user' (including soft deleted)
        $totalCurrent = User::withTrashed()
            ->where('type', 'user')
            ->where('created_at', '<=', $now)
            ->count();

        $totalPrevious = User::withTrashed()
            ->where('type', 'user')
            ->where('created_at', '<=', $thirtyDaysAgo)
            ->count();

        $totalPercentage = $calculatePercentage($totalCurrent, $totalPrevious);
        $totalIsIncrease = $totalCurrent > $totalPrevious;

        return [
            'blocked_accounts' => [
                'title' => 'الحسابات المحظورة',
                'count' => $blockedCurrent,
                'percentage' => abs($blockedPercentage),
                'is_increase' => $blockedIsIncrease,
            ],
            'active_accounts' => [
                'title' => 'الحسابات النشطة',
                'count' => $activeCurrent,
                'percentage' => abs($activePercentage),
                'is_increase' => $activeIsIncrease,
            ],
            'total_individual_users' => [
                'title' => 'إجمالي المستخدمين الأفراد',
                'count' => $totalCurrent,
                'percentage' => abs($totalPercentage),
                'is_increase' => $totalIsIncrease,
            ],
        ];
    }

    public function delete($ids){
        $users = User::whereIn('id', $ids)->get();
        foreach($users as $user){
            $user->forceDelete();
        }
        return $users;
    }

    public function block($ids){
        $users = User::whereIn('id', $ids)->get();
        foreach($users as $user){
            $user->delete();
        }
        return $users;
    }

    public function unblock($ids){
        $users = User::withTrashed()->whereIn('id', $ids)->get();
        foreach($users as $user){
            $user->restore();
        }
        return $users;
    }

    public function toggleActive($ids){
            $users = User::whereIn('id', $ids)->get();
            foreach($users as $user){
                $user->is_active = !$user->is_active;
                $user->save();
            }
        return $users;
    }

    public function export($ids)
    {
        $users = User::whereIn('id', $ids)->get();
        $csvData = [];
        foreach($users as $user) {
            if($user->type == 'user') {
                $csvData[] = [
                    'name' => $user->name,
                    'national_id' => $user->national_id,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                ];
            }

            if($user->type == 'agent') {
                $csvData[] = [
                    'company_name' => $user->agencies?->pluck('name')->implode(','),
                    'name' => $user->name,
                    'national_id' => $user->national_id,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                ];
            }
        }

       $currentUser = auth()->user();

        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
        $media = ExportHelper::exportToMedia($csvData, $currentUser, 'exports', $filename);
        return $media->getFullUrl();
    }

    public function blocklist($data)
    {
        $users = User::withTrashed()->where('type', 'user')->whereNotNull('deleted_at');
        if(isset($data['search']) && $data['search']){
            $users = $users->where('name', 'like', "%{$data['search']}%")
                ->orWhere('national_id', 'like', "%{$data['search']}%")
                ->orWhere('email', 'like', "%{$data['search']}%")
                ->orWhere('phone', 'like', "%{$data['search']}%");
        }
        return $users;
    }
}
