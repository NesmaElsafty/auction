<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $users = $this->userService->index($request->all())->paginate(10);
            $stats = $this->userService->stats();
            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => UserResource::collection($users),
                'stats' => $stats,
                'pagination' => PaginationHelper::paginate($users),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'national_id' => 'required|string|unique:users,national_id',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'summary' => 'nullable|string',
                'link' => 'nullable|url|max:255',
                'password' => 'required|string|min:6',
                'type' => 'required|in:user,admin',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $this->userService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userService->show($id);
            
            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
           $request->validate([
                'name' => 'nullable|string|max:255',
                'national_id' => 'nullable|string|unique:users,national_id,' . $id,
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'summary' => 'nullable|string',
                'link' => 'nullable|url|max:255',
                'password' => 'sometimes|nullable|string|min:6',
                'type' => 'sometimes|nullable|in:user,admin',
                'is_active' => 'sometimes|nullable|boolean',
            ]);

            $user = $this->userService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->userService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // bulk actions
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'nullable|array',
                'ids.*' => 'required|integer|exists:users,id',
                'action' => 'required|in:delete,block,unblock,toggleActive,export',
            ]);
            $ids = $request->ids ?? [];
            switch ($request->action) {
                case 'delete':
                    $result = $this->userService->delete($ids);
                    break;
                case 'block':
                    $result = $this->userService->block($ids);
                    break;
                case 'unblock':
                    $result = $this->userService->unblock($ids);
                    break;
                case 'toggleActive':
                    $result = $this->userService->toggleActive($ids);
                    break;
                case 'export':
                    if($ids == []){
                        $ids = User::where(['type'=> 'user', 'is_active'=> true])->pluck('id');
                    }
                    $result = $this->userService->export($ids);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action',
                    ], 422);
            }
            return response()->json([
                'success' => true,
                'message' => 'Users bulk action performed successfully',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function blocklist(Request $request)
    {
        try {
            $users = $this->userService->blocklist()->paginate(10);
            return response()->json([
                'success' => true,
                'message' => 'Blocklist retrieved successfully',
                'data' => UserResource::collection($users),
                'pagination' => PaginationHelper::paginate($users),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve blocklist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
