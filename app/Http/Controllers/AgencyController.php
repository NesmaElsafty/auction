<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\AgencyResource;
use App\Services\AgencyService;
use Illuminate\Http\Request;
use App\Models\Agency;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;

class AgencyController extends Controller
{
    protected AgencyService $agencyService;

    public function __construct(AgencyService $agencyService)
    {
        $this->agencyService = $agencyService;
    }

    public function index(Request $request)
    {
        try {
            $agencies = $this->agencyService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Agencies retrieved successfully',
                'data' => AgencyResource::collection($agencies),
                'pagination' => PaginationHelper::paginate($agencies),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve agencies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:agencies,name',
                'number' => 'required|string|max:255|unique:agencies,number',
                'date' => 'nullable|date',
                'address' => 'nullable|string|max:255',
                // Bank data
                'bank_name' => 'nullable|string|max:255',
                'bank_account_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
                'bank_address' => 'nullable|string|max:255',
                'IBAN' => 'nullable|string|max:255',
                'SWIFT' => 'nullable|string|max:255',

                'files' => 'nullable|array',
                'files.*' => 'nullable|file',
            ]);

            $user = auth()->user();
            // add user_id to request
            $request->merge(['user_id' => $user->id]);
            $agency = $this->agencyService->store($request->all());

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $agency->addMedia($file)->toMediaCollection('files');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Agency created successfully',
                'data' => new AgencyResource($agency),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create agency',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $agency = Agency::with('user')->find($id);
            if (!$agency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agency not found',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Agency retrieved successfully',
                'data' => new AgencyResource($agency),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agency not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'name' => 'nullable|string|max:255|unique:agencies,name,' . $id,
                'number' => 'nullable|string|max:255|unique:agencies,number,' . $id,
                'date' => 'nullable|date',
                'address' => 'nullable|string|max:255',
                // Bank data
                'bank_name' => 'nullable|string|max:255',
                'bank_account_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:255',
                'bank_address' => 'nullable|string|max:255',
                'IBAN' => 'nullable|string|max:255',
                'SWIFT' => 'nullable|string|max:255',
            ]);

            $agency = $this->agencyService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Agency updated successfully',
                'data' => new AgencyResource($agency),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update agency',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->agencyService->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Agency deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete agency',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // remove files
    public function removeFiles(Request $request)
    {
        try {
            $request->validate([
                'file_id' => 'required|exists:media,id',
            ]);

            $file = Media::find($request->file_id);
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                ], 404);
            }
            $file->delete();
            return response()->json([
                'success' => true,
                'message' => 'File removed successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove file',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // add files
    public function addFiles(Request $request)
    {
        try {

            $request->validate([
                'agency_id' => 'required|exists:agencies,id',
                'files' => 'required|array',
                'files.*' => 'required|file',
            ]);

            $agency = Agency::find($request->agency_id);
            if (!$agency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agency not found',
                ], 404);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $agency->addMedia($file)->toMediaCollection('files');
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Files added successfully',
                'data' => new AgencyResource($agency),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add files',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // user agencies
    public function userAgencies(request $request)
    {
        try {
            $user = auth()->user();
            $agencies = $user->agencies;
            $pagination = null;

            if($user->type == 'admin'){
                $request->validate([
                    'user_id' => 'required|exists:users,id',
                ]);
                $user = User::find($request->user_id);
                if($user->type != 'user'){
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found',
                    ], 404);
                }
                if(count($user->agencies) != 0){
                    $agencies = $this->agencyService->userAgencies($user->id, $request->all())->paginate(10);
                    $pagination = PaginationHelper::paginate($agencies);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User agencies retrieved successfully',
                'data' => AgencyResource::collection($agencies),
                'pagination' => $pagination,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user agencies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // bulk actions 
    public function bulkActions(Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'ids' => 'nullable|array',
                'ids.*' => 'required|exists:agencies,id',
                'user_id' => 'nullable|exists:users,id',
                'action' => 'required|string|in:toggleActivation,export',
            ]);
            switch($request->action){
                case 'toggleActivation':
                    $result = $this->agencyService->toggleActivation($request->ids);
                    break;
                case 'export':
                    if(isset($request->user_id)){
                        $user = User::find($request->user_id);
                        $agencies = $user->agencies;
                        $ids = $agencies->pluck('id');
                    }else{
                        $ids = $request->ids;
                    }
                    $result = $this->agencyService->export($ids);
                    break;
            }
            return response()->json([
                'success' => true,
                'message' => 'Bulk actions performed successfully',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk actions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
