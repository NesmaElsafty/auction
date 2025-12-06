<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\AlertService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;
    protected AlertService $alertService;

    public function __construct(NotificationService $notificationService, AlertService $alertService)
    {
        $this->notificationService = $notificationService;
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        try {
            $notifications = $this->notificationService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
                'data' => NotificationResource::collection($notifications),
                'pagination' => PaginationHelper::paginate($notifications),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications',
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
                'type' => 'required|in:notify,reminder',
                'status' => 'required|in:pending,sent,unsent',
                'is_active' => 'required|boolean',
            ]);

            $notification = $this->notificationService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Notification created successfully',
                'data' => new NotificationResource($notification),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $notification = $this->notificationService->show($id);
            return response()->json([
                'success' => true,
                'message' => 'Notification retrieved successfully',
                'data' => new NotificationResource($notification),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
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
                'type' => 'nullable|in:notify,reminder',
                'status' => 'nullable|in:pending,sent,unsent',
                'is_active' => 'nullable|boolean',
            ]);

            $notification = $this->notificationService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Notification updated successfully',
                'data' => new NotificationResource($notification),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->notificationService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function notificationAlerts($id)
    {
        try {
            $notification = Notification::find($id);
            
            $alerts = $this->alertService->createAlertsFromNotification($notification);
            
            return response()->json([
                'success' => true,
                'message' => 'Alerts created successfully',
                'data' => $alerts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create alerts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
