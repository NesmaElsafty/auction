<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AlertService
{
    public function index($userId)
    {
        $alerts = Alert::where('user_id', $userId)->orderBy('id', 'desc');
        return $alerts;
    }

    public function show($id, $userId)
    {
        $alert = Alert::where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if (!$alert) {
            throw new \Exception('Alert not found');
        }
        
        return $alert;
    }

    public function destroy($id, $userId)
    {
        DB::beginTransaction();
        try {
            $alert = Alert::where('id', $id)
                ->where('user_id', $userId)
                ->first();
            
            if (!$alert) {
                throw new \Exception('Alert not found');
            }

            $alert->delete();

            DB::commit();
            return $alert;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function bulkAction($userId, $data)
    {
        DB::beginTransaction();
        try {
            $ids = $data['ids'] ?? [];
            $action = $data['action'];

            // Ensure all alerts belong to the user
            $alerts = Alert::where('user_id', $userId)
                ->whereIn('id', $ids)
                ->get();

            if ($alerts->isEmpty()) {
                throw new \Exception('No alerts found or alerts do not belong to user');
            }

            $result = [];
            switch ($action) {
                case 'delete':
                    $result = $this->bulkDelete($alerts);
                    break;
                case 'toggleRead':
                    $result = $this->bulkToggleRead($alerts);
                    break;
                default:
                    throw new \Exception('Invalid action');
            }

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function toggleRead($id, $userId)
    {
        DB::beginTransaction();
        try {
            $alert = Alert::where('id', $id)
                ->where('user_id', $userId)
                ->first();
            
            if (!$alert) {
                throw new \Exception('Alert not found');
            }

            $alert->update([
                'is_read' => !$alert->is_read,
            ]);

            DB::commit();
            return $alert;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function bulkDelete($alerts)
    {
        $ids = $alerts->pluck('id')->toArray();
        Alert::whereIn('id', $ids)->delete();
        return ['deleted_count' => count($ids), 'deleted_ids' => $ids];
    }

    protected function bulkToggleRead($alerts)
    {
        $readCount = 0;
        $unreadCount = 0;

        foreach ($alerts as $alert) {
            $alert->update([
                'is_read' => !$alert->is_read,
            ]);
            
            if ($alert->is_read) {
                $readCount++;
            } else {
                $unreadCount++;
            }
        }

        return [
            'updated_count' => $alerts->count(),
            'read_count' => $readCount,
            'unread_count' => $unreadCount,
        ];
    }

    /**
     * Create alerts for all active users when notification status is set to 'sent'
     */
    public static function createAlertsFromNotification($notification)
    {
        // dd($notification);
        if ($notification->status !== 'sent') {
            $notification->update(['status' => 'sent']);

        }

        // Get all active users with type 'user'
        $users = User::where('type', 'user')
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            return false;
        }

        $alerts = [];
        foreach ($users as $user) {
            $alerts[] = [
                'title' => $notification->title,
                'description' => $notification->description,
                'user_id' => $user->id,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert for better performance
        if (!empty($alerts)) {
            Alert::insert($alerts);
        }
    }
}

