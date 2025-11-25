<?php

namespace App\Services;

use App\Models\Agency;
use App\Helpers\ExportHelper;
use Illuminate\Support\Facades\DB;

class AgencyService
{
    public function index($data)
    {
        $query = Agency::with('user');

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Apply user filter if provided
        if (isset($data['user_id']) && $data['user_id']) {
            $query->where('user_id', $data['user_id']);
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $agency = Agency::with('user')->find($id);
        if (!$agency) {
            throw new \Exception('Agency not found');
        }
        return $agency;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $agency = Agency::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'number' => $data['number'],
                'date' => $data['date'] ?? null,
                'address' => $data['address'] ?? null,
                // Bank data
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_address' => $data['bank_address'] ?? null,
                'IBAN' => $data['IBAN'] ?? null,
                'SWIFT' => $data['SWIFT'] ?? null,
            ]);

            DB::commit();
            return $agency->load('user');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $agency = Agency::find($id);
            if (!$agency) {
                throw new \Exception('Agency not found');
            }

            $agency->update([
                'user_id' => $data['user_id'] ?? $agency->user_id,
                'name' => $data['name'] ?? $agency->name,
                'number' => $data['number'] ?? $agency->number,
                'date' => $data['date'] ?? $agency->date,
                'address' => $data['address'] ?? $agency->address,
                // Bank data
                'bank_name' => $data['bank_name'] ?? $agency->bank_name,
                'bank_account_name' => $data['bank_account_name'] ?? $agency->bank_account_name,
                'bank_account_number' => $data['bank_account_number'] ?? $agency->bank_account_number,
                'bank_address' => $data['bank_address'] ?? $agency->bank_address,
                'IBAN' => $data['IBAN'] ?? $agency->IBAN,
                'SWIFT' => $data['SWIFT'] ?? $agency->SWIFT,
            ]);

            DB::commit();
            return $agency->load('user');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $agency = Agency::find($id);
            if (!$agency) {
                throw new \Exception('Agency not found');
            }

            $agency->delete();

            DB::commit();
            return $agency;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function userAgencies($user_id , $data)
    {
        $agencies = Agency::where('user_id', $user_id);
        if($data['search']){
            $agencies = $agencies->where('name', 'like', "%{$data['search']}%")
                ->orWhere('number', 'like', "%{$data['search']}%")
                ->orWhere('address', 'like', "%{$data['search']}%");
        }
        if(isset($data['is_active']) && $data['is_active'] !== 'all'){
            $agencies = $agencies->where('is_active', $data['is_active']);
        }
        if(isset($data['sorted_by']) && $data['sorted_by'] !== 'all'){
            switch ($data['sorted_by']) {
                case 'name':
                    $agencies = $agencies->orderBy('name', 'asc');
                    break;
                case 'oldest':
                    $agencies = $agencies->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $agencies = $agencies->orderBy('created_at', 'desc');
                    break;
            }
        }

        return $agencies;
    }

    // toggle activation
    public function toggleActivation($ids)
    {
        $agencies = [];
        foreach($ids as $id){
            $agency = Agency::find($id);
            $agency->update(['is_active' => !$agency->is_active]);
            $agencies[] = $agency;
        }

        return $agencies;
    }

    public function export($ids)
    {
        $agencies = Agency::whereIn('id', $ids)->get();
        $csvData = [];
        foreach($agencies as $agency) {
            $csvData[] = [
                'name' => $agency->name,
                'number' => $agency->number,
                'address' => $agency->address,
                'bank_name' => $agency->bank_name,
                'bank_account_name' => $agency->bank_account_name,
                'bank_account_number' => $agency->bank_account_number,
                'bank_address' => $agency->bank_address,
                'IBAN' => $agency->IBAN,
                'SWIFT' => $agency->SWIFT,
                'is_active' => $agency->is_active,
                'created_at' => $agency->created_at,
                'updated_at' => $agency->updated_at,
            ];
        }
        $currentUser = auth()->user();
        $filename = 'agencies_export_' . now()->format('Ymd_His') . '.csv';
        $media = ExportHelper::exportToMedia($csvData, $currentUser, 'exports', $filename);
        return $media->getFullUrl();
    }
}

