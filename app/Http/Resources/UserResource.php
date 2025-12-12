<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsapp_number,
            'user_type' => $this->user_type,
            'fin' => $this->fin,
            'age' => $this->age,
            'gender' => $this->gender,
            'qr_code' => $this->qr_code,
            'is_verified' => $this->is_verified,
            'total_points' => $this->total_points,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
