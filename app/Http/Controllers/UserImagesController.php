<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImageResource;
use Illuminate\Http\Request;

class UserImagesController extends Controller
{
    //
    public function store()
    {
        $data = request()->validate([
            'image' => '',
            'width' => '',
            'height' => '',
            'location' => ''
        ]);
        $imgPath = $data['image']->store('user-images', 'public');

        $userImage = auth()->user()->images()->create([
            'path' => $imgPath,
            'width' => $data['width'],
            'height' => $data['height'],
            'location' => $data['location'],
        ]);

        return new UserImageResource($userImage);
    }
}
