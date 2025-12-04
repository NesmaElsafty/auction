<?php

namespace App\Services;

use App\Models\Term;
use Illuminate\Support\Facades\DB;

class TermService
{
    public function index($data)
    {
        $query = Term::query();

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter if provided
        if (isset($data['type']) && $data['type']) {
            $query->where('type', $data['type']);
        }

        // Apply is_active filter if provided
        if (isset($data['is_active']) && $data['is_active'] !== 'all') {
            $query->where('is_active', filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($data['sorted_by']) && $data['sorted_by'] !== 'all') {
            switch ($data['sorted_by']) {
                case 'title':
                    $query->orderBy('title', 'asc');
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
        $term = Term::find($id);
        if (!$term) {
            throw new \Exception('Term not found');
        }
        return $term;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $term = Term::create([
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            DB::commit();
            return $term;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $term = Term::find($id);
            if (!$term) {
                throw new \Exception('Term not found');
            }

            $term->update([
                'title' => $data['title'] ?? $term->title,
                'description' => $data['description'] ?? $term->description,
                'type' => $data['type'] ?? $term->type,
                'is_active' => isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : $term->is_active,
            ]);

            DB::commit();
            return $term;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $term = Term::find($id);
            if (!$term) {
                throw new \Exception('Term not found');
            }

            $term->delete();

            DB::commit();
            return $term;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

