<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\ScreenResource;
use App\Services\ScreenService;
use Illuminate\Http\Request;
use App\Models\Screen;
class ScreenController extends Controller
{
    protected ScreenService $screenService;

    public function __construct(ScreenService $screenService)
    {
        $this->screenService = $screenService;
    }

    public function index(Request $request)
    {
        try {
            $screens = $this->screenService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Screens retrieved successfully',
                'data' => ScreenResource::collection($screens),
                'pagination' => PaginationHelper::paginate($screens),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve screens',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
          $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
            ]);

            $screen = $this->screenService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Screen created successfully',
                'data' => new ScreenResource($screen),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create screen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            $screen = Screen::with('category', 'inputs', 'inputs.options')->find($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Screen retrieved successfully',
                'data' => new ScreenResource($screen),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Screen not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
          $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
            ]);

            $screen = $this->screenService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Screen updated successfully',
                'data' => new ScreenResource($screen),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update screen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->screenService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Screen deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete screen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
