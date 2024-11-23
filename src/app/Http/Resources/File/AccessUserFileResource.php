<?php

namespace App\Http\Resources\File;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccessUserFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fullname' => "{$this->first_name} {$this->last_name}",
            'email' => $this->email,
            'type' => $this->pivot->access_type
        ];
    }
}
