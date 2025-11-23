<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\RegionResource;
use App\Services\RegionService;
use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    protected RegionService $regionService;

    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    public function index(Request $request)
    {
        try {
            $regions = $this->regionService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Regions retrieved successfully',
                'data' => RegionResource::collection($regions),
                'pagination' => PaginationHelper::paginate($regions),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve regions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'city_id' => 'required|exists:cities,id',
            ]);

            $region = $this->regionService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Region created successfully',
                'data' => new RegionResource($region),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create region',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $region = Region::with('city')->find($id);
            if (!$region) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Region retrieved successfully',
                'data' => new RegionResource($region),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'city_id' => 'nullable|exists:cities,id',
            ]);

            $region = $this->regionService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Region updated successfully',
                'data' => new RegionResource($region),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update region',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->regionService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Region deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete region',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
