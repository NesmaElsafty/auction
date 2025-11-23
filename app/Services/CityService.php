<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Facades\DB;

class CityService
{
    public function index($data)
    {
        $query = City::with('regions');

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        // Default ordering
        $query->orderBy('name', 'asc');

        return $query;
    }

    public function show($id)
    {
        $city = City::with('regions')->find($id);
        if (!$city) {
            throw new \Exception('City not found');
        }
        return $city;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $city = City::create([
                'name' => $data['name'],
            ]);

            DB::commit();
            return $city->load('regions');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $city = City::find($id);
            if (!$city) {
                throw new \Exception('City not found');
            }

            $city->update([
                'name' => $data['name'] ?? $city->name,
            ]);

            DB::commit();
            return $city->load('regions');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $city = City::find($id);
            if (!$city) {
                throw new \Exception('City not found');
            }

            $city->delete();

            DB::commit();
            return $city;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

