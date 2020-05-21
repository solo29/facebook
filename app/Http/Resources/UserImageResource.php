<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'user-images',
                'user_image_id' => $this->id,
                'attributes' => [
                    'path' => Storage::url($this->path),
                    'width' => ($this->width),
                    'height' => ($this->height),
                    'location' => ($this->location)
                ]
            ],
            'links' => [
                'self' => url('/users/' . $this->user_id)
            ]
        ];
    }
}
