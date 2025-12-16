<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingService
{
    public function index($data)
    {
        $query = Setting::query();

        if (isset($data['type']) && $data['type'] !== 'all') {
            $query->where('type', $data['type']);
        }

        return $query;
    }

    public function show($id)
    {
        $setting = Setting::find($id);
        if (!$setting) {
            throw new \Exception('Setting not found');
        }
        return $setting;
    }

    public function store($data)
    {
            $setting = Setting::updateOrCreate([
                'name' => $data['name'],
                'type' => $data['type']
            ], [
                'value' => $data['value'],
            ]);
            return $setting;
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $setting = Setting::find($id);
            if (!$setting) {
                throw new \Exception('Setting not found');
            }

            $setting->update([
                'type' => $data['type'] ?? $setting->type,
                'name' => $data['name'] ?? $setting->name,
                'value' => $data['value'] ?? $setting->value,
            ]);

            DB::commit();
            return $setting;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $setting = Setting::find($id);
            if (!$setting) {
                throw new \Exception('Setting not found');
            }

            $setting->delete();

            DB::commit();
            return $setting;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

