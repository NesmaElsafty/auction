<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'number' => $this->number,
            'date' => $this->date,
            'address' => $this->address,
            'bank_data' => [
                'bank_name' => $this->bank_name,
                'bank_account_name' => $this->bank_account_name,
                'bank_account_number' => $this->bank_account_number,
                'bank_address' => $this->bank_address,
                'IBAN' => $this->IBAN,
                'SWIFT' => $this->SWIFT,
            ],
            'files' => $this->getMedia('files')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => str_replace('public/', '', $media->getUrl()),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}

