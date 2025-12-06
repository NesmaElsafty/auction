<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\AlertResource;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            $alerts = $this->alertService->index($userId)->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Alerts retrieved successfully',
                'data' => AlertResource::collection($alerts),
                'pagination' => PaginationHelper::paginate($alerts),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve alerts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $userId = Auth::id();
            $alert = $this->alertService->show($id, $userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Alert retrieved successfully',
                'data' => new AlertResource($alert),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alert not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $userId = Auth::id();
            $this->alertService->destroy($id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Alert deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete alert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:alerts,id',
                'action' => 'required|in:delete,toggleRead',
            ]);

            $userId = Auth::id();
            $result = $this->alertService->bulkAction($userId, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Bulk action performed successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleRead($id)
    {
        try {
            $userId = Auth::id();
            $alert = $this->alertService->toggleRead($id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Alert read status toggled successfully',
                'data' => new AlertResource($alert),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle alert read status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
