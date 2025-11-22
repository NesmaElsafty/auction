<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\InputResource;
use App\Services\InputService;
use Illuminate\Http\Request;

class InputController extends Controller
{
    protected InputService $inputService;

    public function __construct(InputService $inputService)
    {
        $this->inputService = $inputService;
    }

    public function index(Request $request)
    {
        try {
            $inputs = $this->inputService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Inputs retrieved successfully',
                'data' => InputResource::collection($inputs),
                'pagination' => PaginationHelper::paginate($inputs),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inputs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'screen_id' => 'required|exists:screens,id',
                'name' => 'required|string|max:255',
                'type' => 'required|in:text,number,email,password,date,time,datetime,checkbox,radio,select,textarea,file,image,video,audio',
                'placeholder' => 'nullable|string|max:255',
                'label' => 'nullable|string|max:255',
                'is_required' => 'nullable|boolean',
            ]);

            $input = $this->inputService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Input created successfully',
                'data' => new InputResource($input),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $input = $this->inputService->show($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Input retrieved successfully',
                'data' => new InputResource($input),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'screen_id' => 'nullable|exists:screens,id',
                'name' => 'nullable|string|max:255',
                'type' => 'nullable|in:text,number,email,password,date,time,datetime,checkbox,radio,select,textarea,file,image,video,audio',
                'placeholder' => 'nullable|string|max:255',
                'label' => 'nullable|string|max:255',
                'is_required' => 'nullable|boolean',
            ]);

            $input = $this->inputService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Input updated successfully',
                'data' => new InputResource($input),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->inputService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Input deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
