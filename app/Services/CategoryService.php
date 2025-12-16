<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Screen;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function index($data)
    {
        $query = Category::query();

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply type filter if provided
        if (isset($data['type']) && $data['type']) {
            $query->where('type', $data['type']);
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            throw new \Exception('Category not found');
        }
        return $category;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $category = Category::create([
                'name' => $data['name'],
                'start_precentage' => $data['start_precentage'] ?? null,
                'end_precentage' => $data['end_precentage'] ?? null,
                'title' => $data['title'] ?? null,
                'content' => $data['content'] ?? null,
                'type' => $data['type'],
                'minimum_bid_increment' => $data['minimum_bid_increment'] ?? null,
                'maximum_bid_increment' => $data['maximum_bid_increment'] ?? null,
            ]);


            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $category = Category::find($id);
            if (!$category) {
                throw new \Exception('Category not found');
            }

            $category->update([
                'name' => $data['name'] ?? $category->name,
                'start_precentage' => $data['start_precentage'] ?? $category->start_precentage,
                'end_precentage' => $data['end_precentage'] ?? $category->end_precentage,
                'title' => $data['title'] ?? $category->title,
                'content' => $data['content'] ?? $category->content,
                'type' => $data['type'] ?? $category->type,
                'minimum_bid_increment' => $data['minimum_bid_increment'] ?? $category->minimum_bid_increment,
                'maximum_bid_increment' => $data['maximum_bid_increment'] ?? $category->maximum_bid_increment,
            ]);

                DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $category = Category::find($id);
            if (!$category) {
                throw new \Exception('Category not found');
            }

            $category->delete();

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

