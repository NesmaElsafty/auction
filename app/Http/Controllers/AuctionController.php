<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\AuctionResource;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use App\Models\ItemData;
use App\Models\Auction;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AuctionController extends Controller
{
    protected AuctionService $auctionService;

    public function __construct(AuctionService $auctionService)
    {
        $this->auctionService = $auctionService;
    }

    public function index(Request $request)
    {
        try {
            $auctions = $this->auctionService->index($request->all())->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'Auctions retrieved successfully',
                'data' => AuctionResource::collection($auctions),
                'pagination' => PaginationHelper::paginate($auctions),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve auctions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required',
                'user_type' => 'required|in:user,agency',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'nullable|in:online,both',
                'is_infaz' => 'nullable|boolean',
                'start_price' => 'nullable|numeric|min:0',
                'deposit_price' => 'nullable|numeric|min:0',
                'minimum_bid_increment' => 'nullable|integer|min:1',
                'youtube_link' => 'nullable|url|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'awarding_period_days' => 'nullable|integer|min:1',
                'is_active' => 'nullable|boolean',
                'is_approved' => 'nullable|boolean',
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'items' => 'required|array',
                'items.*.input_id' => 'required|exists:inputs,id',
                'items.*.label' => 'required|string|max:255',
                'items.*.value' => 'required|string|max:255',
            ]);

            $user = auth()->user();
            $auction = $this->auctionService->store($request->all(), $user);

            // upload images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $auction->addMedia($image)->toMediaCollection('images');
                }
            }

            // upload items
            if ($request->items && count($request->items) > 0) {
                foreach ($request->items as $item) {
                    $itemData = ItemData::create([
                        'auction_id' => $auction->id,
                        'input_id' => $item['input_id'],
                        'label' => $item['label'],
                        'value' => $item['value'],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Auction created successfully',
                'data' => new AuctionResource($auction),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create auction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $auction = $this->auctionService->show($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Auction retrieved successfully',
                'data' => new AuctionResource($auction),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auction not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'agency_id' => 'nullable|exists:agencies,id',
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'type' => 'nullable|in:online,both',
                'is_infaz' => 'nullable|boolean',
                'start_price' => 'nullable|numeric|min:0',
                'end_price' => 'nullable|numeric|min:0',
                'deposit_price' => 'nullable|numeric|min:0',
                'minimum_bid_increment' => 'nullable|integer|min:1',
                'youtube_link' => 'nullable|url|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'awarding_period_days' => 'nullable|integer|min:1',
                'status' => 'nullable|in:pending,current,completed,cancelled',
                'is_active' => 'nullable|boolean',
                'is_approved' => 'nullable|boolean',
            ]);

            $user = auth()->user();
            $auction = $this->auctionService->update($id, $request->all(), $user);

            // update items
            if ($request->items && count($request->items) > 0) {
                foreach ($request->items as $item) {
                    $itemData = ItemData::where('auction_id', $id)->where('input_id', $item['input_id'])->first();
                    if ($itemData) {
                        $itemData->update([
                            'label' => $item['label'],
                            'value' => $item['value'],
                        ]);
                    } else {
                        $itemData = ItemData::create([
                            'auction_id' => $id,
                            'input_id' => $item['input_id'],
                            'label' => $item['label'],
                            'value' => $item['value'],
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Auction updated successfully',
                'data' => new AuctionResource($auction),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update auction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $this->auctionService->destroy($id, $user);

            return response()->json([
                'success' => true,
                'message' => 'Auction deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete auction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // add images
    public function addImages(Request $request)
    {
        try {
            $request->validate([
                'auction_id' => 'required|exists:auctions,id',
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
            $auction = Auction::find($request->auction_id);

            $validation =$this->auctionService->verifyOwnership($auction, auth()->user());
            if (!$validation) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not the owner of this auction',
                ], 404);
            }
            
            foreach ($request->file('images') as $image) {
                $auction->addMedia($image)->toMediaCollection('images');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Images added successfully',
                'data' => new AuctionResource($auction),
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add images',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // remove images
    public function removeImages(Request $request)
    {
        try {
            $request->validate([
                'image_id' => 'required|exists:media,id',
            ]);
            $image = Media::find($request->image_id);
            $auction = Auction::find($image->model_id);
            
            $validation =$this->auctionService->verifyOwnership($auction, auth()->user());

            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully',
                'data' => new AuctionResource($auction),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function userAuctions(Request $request)
    {
        try {
            $user = auth()->user();
            $data = array_merge($request->all(), ['user_id' => $user->id]);
            $auctions = $this->auctionService->index($data)->paginate(10);
            
            return response()->json([
                'success' => true,
                'message' => 'User auctions retrieved successfully',
                'data' => AuctionResource::collection($auctions),
                'pagination' => PaginationHelper::paginate($auctions),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user auctions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
