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
        $image = $this->getFirstMediaUrl('images') ? str_replace('public/', '', $this->getFirstMediaUrl('images')) : 'https://placehold.co/600x400?text=No+Image&font=roboto';
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'image' => $image,
            'screens' => ScreenResource::collection($this->screens),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

