<?php

namespace App\Services;

use App\Models\Option;

class OptionService
{
    public function index($data)
    {
        $query = Option::with('input');

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('value', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            });
        }

        // Apply input filter if provided
        if (isset($data['input_id']) && $data['input_id']) {
            $query->where('input_id', $data['input_id']);
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $option = Option::with('input')->find($id);
        if (!$option) {
            throw new \Exception('Option not found');
        }
        return $option;
    }

    public function store($data)
    {
        $option = Option::create([
            'input_id' => $data['input_id'],
            'value' => $data['value'],
            'label' => $data['label'],
        ]);

        return $option->load('input');
    }

    public function update($id, $data)
    {
        $option = Option::find($id);
        if (!$option) {
            throw new \Exception('Option not found');
        }

        $option->update([
            'input_id' => $data['input_id'] ?? $option->input_id,
            'value' => $data['value'] ?? $option->value,
            'label' => $data['label'] ?? $option->label,
        ]);

        return $option->load('input');
    }

    public function destroy($id)
    {
        $option = Option::find($id);
        if (!$option) {
            throw new \Exception('Option not found');
        }
        
        $option->delete();
        return $option;
    }
}

