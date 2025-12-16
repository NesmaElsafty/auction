<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // imaege default if not found
        // dd($this->screens);
        $image = $this->getFirstMediaUrl('images') ? str_replace('public/', '', $this->getFirstMediaUrl('images')) : 'https://placehold.co/600x400?text=No+Image&font=roboto';
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_precentage' => $this->start_precentage,
            'end_precentage' => $this->end_precentage,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'minimum_bid_increment' => $this->minimum_bid_increment,
            'maximum_bid_increment' => $this->maximum_bid_increment,
            'image' => $image,
            'screens' => ScreenResource::collection($this->screens),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

