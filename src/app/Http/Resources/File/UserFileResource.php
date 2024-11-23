<?php

namespace App\Http\Resources\File;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'file_id' => $this->file_id,
            'name' => $this->name,
            'url' => url("/files/{$this->file_id}"),
            'accesses' => AccessUserFileResource::collection($this->usersWithAccess)
        ];
    }
}
