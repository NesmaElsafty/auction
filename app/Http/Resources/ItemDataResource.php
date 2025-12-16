<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemDataResource extends JsonResource
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
            'input' => [
                'id' => $this->input->id,
                'name' => $this->input->name,
                'type' => $this->input->type,
            ],
            'file' => [
                'id' => $this->file?->id,
                'url' => str_replace('public/', '', $this->file?->getUrl()),
            ],
            'label' => $this->label,
            'value' => $this->value,
        ];
    }
}

