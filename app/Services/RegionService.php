<?php

namespace App\Services;

use App\Models\Region;
use Illuminate\Support\Facades\DB;

class RegionService
{
    public function index($data)
    {
        $query = Region::with('city');

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply city filter if provided
        if (isset($data['city_id']) && $data['city_id']) {
            $query->where('city_id', $data['city_id']);
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $region = Region::with('city')->find($id);
        if (!$region) {
            throw new \Exception('Region not found');
        }
        return $region;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $region = Region::create([
                'name' => $data['name'],
                'city_id' => $data['city_id'],
            ]);

            DB::commit();
            return $region->load('city');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $region = Region::find($id);
            if (!$region) {
                throw new \Exception('Region not found');
            }

            $region->update([
                'name' => $data['name'] ?? $region->name,
                'city_id' => $data['city_id'] ?? $region->city_id,
            ]);

            DB::commit();
            return $region->load('city');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $region = Region::find($id);
            if (!$region) {
                throw new \Exception('Region not found');
            }

            $region->delete();

            DB::commit();
            return $region;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

