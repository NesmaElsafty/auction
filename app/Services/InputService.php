<?php

namespace App\Services;

use App\Models\Input;

class InputService
{
    public function index($data)
    {
        $query = Input::with(['screen', 'options']);

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('placeholder', 'like', "%{$search}%");
            });
        }

        // Apply screen filter if provided
        if (isset($data['screen_id']) && $data['screen_id']) {
            $query->where('screen_id', $data['screen_id']);
        }

        // Apply type filter if provided
        if (isset($data['type']) && $data['type']) {
            $query->where('type', $data['type']);
        }

        // Apply required filter if provided
        if (isset($data['is_required'])) {
            $query->where('is_required', filter_var($data['is_required'], FILTER_VALIDATE_BOOLEAN));
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $input = Input::with(['screen', 'options'])->find($id);
        if (!$input) {
            throw new \Exception('Input not found');
        }
        return $input;
    }

    public function store($data)
    {
        $input = Input::create([
            'screen_id' => $data['screen_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'placeholder' => $data['placeholder'] ?? null,
            'label' => $data['label'] ?? null,
            'is_required' => $data['is_required'] ?? false,
        ]);

        return $input->load(['screen', 'options']);
    }

    public function update($id, $data)
    {
        $input = Input::find($id);
        if (!$input) {
            throw new \Exception('Input not found');
        }

        $input->update([
            'screen_id' => $data['screen_id'] ?? $input->screen_id,
            'name' => $data['name'] ?? $input->name,
            'type' => $data['type'] ?? $input->type,
            'placeholder' => $data['placeholder'] ?? $input->placeholder,
            'label' => $data['label'] ?? $input->label,
            'is_required' => $data['is_required'] ?? $input->is_required,
        ]);

        return $input->load(['screen', 'options']);
    }

    public function destroy($id)
    {
        $input = Input::find($id);
        if (!$input) {
            throw new \Exception('Input not found');
        }
        
        $input->delete();
        return $input;
    }
}

