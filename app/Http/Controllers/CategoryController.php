<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Screen;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        try {
            $categories = $this->categoryService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => CategoryResource::collection($categories),
                'pagination' => PaginationHelper::paginate($categories),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
           $request->validate([
                'name' => 'required|string|max:255',
                'start_precentage' => 'nullable|numeric|min:0|max:100',
                'end_precentage' => 'nullable|numeric|min:0|max:100',
                'title' => 'nullable|string|max:255',
                'content' => 'nullable|string',
                'type' => 'required|in:terms,contracts',
                'screens' => 'nullable|array',
                'screens.*.title' => 'required|string|max:255',
                'screens.*.description' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $category = $this->categoryService->store($request->all());

            // upload image
            if ($request->hasFile('image')) {
                $category->addMediaFromRequest('image')
                    ->toMediaCollection('images');
            }

            // create screens
            if ($request->has('screens')) {
                $screens = $request->screens;
                foreach ($screens as $screen) {
                    Screen::create([
                        'title' => $screen['title'],
                        'description' => $screen['description'],
                        'category_id' => $category->id,
                    ]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => new CategoryResource($category),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::with('screens', 'screens.inputs', 'screens.inputs.options')->find($id);
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => new CategoryResource($category),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
          $request->validate([
                'name' => 'nullable|string|max:255',
                'start_precentage' => 'nullable|numeric|min:0|max:100',
                'end_precentage' => 'nullable|numeric|min:0|max:100',
                'title' => 'nullable|string|max:255',
                'content' => 'nullable|string',
                'type' => 'nullable|in:terms,contracts',
                'screens' => 'nullable|array',
                'screens.*.title' => 'required|string|max:255',
                'screens.*.description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $category = $this->categoryService->update($id, $request->all());

            // upload image
            if ($request->hasFile('image')) {
                $category->addMediaFromRequest('image')
                    ->toMediaCollection('images');
            }

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => new CategoryResource($category),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
