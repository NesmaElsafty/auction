<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $auction = $this;
        switch($this->start_date){
            case $this->start_date > now():
                $auction->status = 'Pending';
                break;
            case $this->start_date < now() && $this->end_date > now():
                $auction->status = 'Current';
                break;
            case $this->end_date < now():
                $auction->status = 'Completed';
                break;
        }
        $auction->save();
        return [
            'id' => $this->id,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'user' => $this->when($this->relationLoaded('user'), function () {
                if ($this->user_type === "App\\Models\\User") {
                    return new UserResource($this->user);
                }elseif($this->user_type === "App\\Models\\Agency") {
                    return new AgencyResource($this->user);
                }else{
                    return null;
                }
            }),
            'user_type' => $this->user_type,
            'post_data' => [
                'post_type' => $this->post_type,
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'is_infaz' => $this->is_infaz,

                'start_price' => $this->start_price,
                'end_price' => $this->end_price == null ? $this->start_price : $this->end_price,
                'deposit_price' => $this->deposit_price,
                'minimum_bid_increment' => $this->minimum_bid_increment,
                
                'youtube_link' => $this->youtube_link,
                
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'awarding_period_days' => $this->awarding_period_days,

                'location' => $this->location,
                'lat' => $this->lat,
                'long' => $this->long,
                'viewing_date' => $this->viewing_date,
                
                'status' => $this->status,
                'is_active' => $this->is_active,
                'is_approved' => $this->is_approved,
                'images' => $this->getMedia('images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => str_replace('public/', '', $media->getUrl()),
                    ];
                }),
                'documents' => $this->getMedia('documents')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => str_replace('public/', '', $media->getUrl()),
                    ];
                }),
                
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'item_data' => ItemDataResource::collection($this->whenLoaded('itemData')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}

