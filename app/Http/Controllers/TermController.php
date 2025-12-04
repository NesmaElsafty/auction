<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\TermResource;
use App\Services\TermService;
use Illuminate\Http\Request;

class TermController extends Controller
{
    protected TermService $termService;

    public function __construct(TermService $termService)
    {
        $this->termService = $termService;
    }

    public function index(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:terms,privacy_policy,faqs',
            ]);
            $terms = $this->termService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Terms retrieved successfully',
                'data' => TermResource::collection($terms),
                'pagination' => PaginationHelper::paginate($terms),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve terms',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:terms,privacy_policy,faqs',
                'is_active' => 'required|boolean',
            ]);

            $term = $this->termService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Term created successfully',
                'data' => new TermResource($term),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $term = $this->termService->show($id);
            return response()->json([
                'success' => true,
                'message' => 'Term retrieved successfully',
                'data' => new TermResource($term),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Term not found',
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
                'is_active' => 'nullable|boolean',
            ]);

            $term = $this->termService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Term updated successfully',
                'data' => new TermResource($term),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->termService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Term deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
