<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\SettingResource;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index(Request $request)
    {
        try {
            $settings = $this->settingService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Settings retrieved successfully',
                'data' => SettingResource::collection($settings),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if($request->name == "auction_percentage_alert"){
                $request->validate([
                    'value' => 'required|array',
                    'value.*' => 'required|numeric|min:0|max:100',
                ]);

                $value = json_encode($request->value);
                $request->merge(['value' => $value]);
            }else{
                $request->validate([
                    'type' => 'required|string|max:255',
                    'name' => 'required|string|max:255',
                    'value' => 'nullable|numeric|min:0|max:100',
                ]);
            }
            $setting = $this->settingService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully',
                'data' => new SettingResource($setting),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($name)
    {
        try {
            $setting = $this->settingService->show($name);
            
            return response()->json([
                'success' => true,
                'message' => 'Setting retrieved successfully',
                'data' => new SettingResource($setting),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
