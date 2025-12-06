<?php

namespace App\Services;

use App\Models\Notification;
use App\Services\AlertService;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function index($data)
    {
        $query = Notification::query();

        // Apply search filter
        if (isset($data['search']) && $data['search']) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter if provided
        if (isset($data['type']) && $data['type'] !== 'all') {
            $query->where('type', $data['type']);
        }

        // Apply status filter if provided
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where('status', $data['status']);
        }

        // Apply is_active filter if provided
        if (isset($data['is_active']) && $data['is_active'] !== 'all') {
            $query->where('is_active', filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($data['sorted_by']) && $data['sorted_by'] !== 'all') {
            switch ($data['sorted_by']) {
                case 'title':
                    $query->orderBy('title', 'asc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Default ordering
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function show($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            throw new \Exception('Notification not found');
        }
        return $notification;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $notification = Notification::create([
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'status' => $data['status'] ?? 'pending',
                'is_active' => $data['is_active'] ?? true,
            ]);

            // If status is 'sent', create alerts for all active users
            if ($notification->status === 'sent') {
                AlertService::createAlertsFromNotification($notification);
            }

            DB::commit();
            return $notification;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $notification = Notification::find($id);
            if (!$notification) {
                throw new \Exception('Notification not found');
            }

            $oldStatus = $notification->status;
            
            $notification->update([
                'title' => $data['title'] ?? $notification->title,
                'description' => $data['description'] ?? $notification->description,
                'type' => $data['type'] ?? $notification->type,
                'status' => $data['status'] ?? $notification->status,
                'is_active' => isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : $notification->is_active,
            ]);

            // If status changed to 'sent', create alerts for all active users
            if ($notification->status === 'sent' && $oldStatus !== 'sent') {
                AlertService::createAlertsFromNotification($notification);
            }

            DB::commit();
            return $notification;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $notification = Notification::find($id);
            if (!$notification) {
                throw new \Exception('Notification not found');
            }

            $notification->delete();

            DB::commit();
            return $notification;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

