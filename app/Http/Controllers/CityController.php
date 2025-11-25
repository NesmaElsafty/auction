<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Services\CityService;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Region;

class CityController extends Controller
{
    protected CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index(Request $request)
    {
        try {

            $cities = $this->cityService->index($request->all())->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Cities retrieved successfully',
                'data' => CityResource::collection($cities),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'regions' => 'nullable|array',
                'regions.*.name' => 'required|string|max:255',
            ]);


            $city = $this->cityService->store($request->all());

            if ($request->has('regions')) {
                
                foreach ($request->regions as $region) {
                    Region::create([
                        'city_id' => $city->id,
                        'name' => $region['name'],
                    ]);
                }
            }
            $city->load('regions');
            return response()->json([
                'success' => true,
                'message' => 'City created successfully',
                'data' => new CityResource($city),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $city = City::with('regions')->find($id);
            if (!$city) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'City retrieved successfully',
                'data' => new CityResource($city),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'City not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'regions' => 'nullable|array',
                'regions.*.name' => 'nullable|string|max:255',
            ]);

            $city = $this->cityService->update($id, $request->all());

            if ($request->has('regions')) {
                $city->regions()->delete();
                foreach ($request->regions as $region) {
                    $city->regions()->create([
                            'name' => $region['name'],
                        ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'City updated successfully',
                'data' => new CityResource($city),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->cityService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'City deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete city',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
