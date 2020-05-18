<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $collects = 'App\Http\Resources\PostResource';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => url('/posts')
            ]
        ];
    }
}
