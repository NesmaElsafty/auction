<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InputResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'screen_id' => $this->screen_id,
            'name' => $this->name,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'label' => $this->label,
            'is_required' => $this->is_required,
            'screen' => new ScreenResource($this->whenLoaded('screen')),
            'options' => OptionResource::collection($this->options),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

