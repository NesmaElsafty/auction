<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\OptionResource;
use App\Services\OptionService;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    protected OptionService $optionService;

    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }

    public function index(Request $request)
    {
        try {
            $options = $this->optionService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Options retrieved successfully',
                'data' => OptionResource::collection($options),
                'pagination' => PaginationHelper::paginate($options),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve options',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'input_id' => 'required|exists:inputs,id',
                'value' => 'required|string|max:255',
                'label' => 'required|string|max:255',
            ]);

            $option = $this->optionService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Option created successfully',
                'data' => new OptionResource($option),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create option',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $option = $this->optionService->show($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Option retrieved successfully',
                'data' => new OptionResource($option),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Option not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'input_id' => 'nullable|exists:inputs,id',
                'value' => 'nullable|string|max:255',
                'label' => 'nullable|string|max:255',
            ]);

            $option = $this->optionService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Option updated successfully',
                'data' => new OptionResource($option),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update option',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->optionService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Option deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete option',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
