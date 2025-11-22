<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
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
            'input_id' => $this->input_id,
            'value' => $this->value,
            'label' => $this->label,
            'input' => $this->when($this->relationLoaded('input'), function () {
                return [
                    'id' => $this->input->id,
                    'screen_id' => $this->input->screen_id,
                    'name' => $this->input->name,
                    'type' => $this->input->type,
                    'label' => $this->input->label,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

