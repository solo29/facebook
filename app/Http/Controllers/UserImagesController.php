<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

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


        if (!\App::runningUnitTests()) {
            Image::make($data['image'])
                ->fit($data['width'], $data['height'])
                ->save(storage_path('app/public/user-images/' . $data['image']->hashName()));
        }

        $userImage = auth()->user()->images()->create([
            'path' => $imgPath,
            'width' => $data['width'],
            'height' => $data['height'],
            'location' => $data['location'],
        ]);

        return new UserImageResource($userImage);
    }
}
