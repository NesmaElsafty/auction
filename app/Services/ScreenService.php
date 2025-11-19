<?php

namespace App\Services;

use App\Models\Screen;

class ScreenService
{
    public function index($data)
    {
        $query = Screen::with('category');

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter if provided
        if (isset($data['category_id']) && $data['category_id']) {
            $query->where('category_id', $data['category_id']);
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $screen = Screen::with('category')->find($id);
        if (!$screen) {
            throw new \Exception('Screen not found');
        }
        return $screen;
    }

    public function store($data)
    {
        $screen = Screen::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'],
        ]);

        return $screen->load('category');
    }

    public function update($id, $data)
    {
        $screen = Screen::find($id);
        if (!$screen) {
            throw new \Exception('Screen not found');
        }

        $screen->update([
            'title' => $data['title'] ?? $screen->title,
            'description' => $data['description'] ?? $screen->description,
            'category_id' => $data['category_id'] ?? $screen->category_id,
        ]);

        return $screen->load('category');
    }

    public function destroy($id)
    {
        $screen = Screen::find($id);
        if (!$screen) {
            throw new \Exception('Screen not found');
        }
        
        $screen->delete();
        return $screen;
    }
}

