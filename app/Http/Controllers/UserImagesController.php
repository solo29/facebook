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

        $filename = $data['image']->hashName();

        $storagePath = storage_path('app/public/user-images/' . $filename);

        if (\App::runningUnitTests())
            $storagePath = storage_path('framework/testing/disks/user-images/' . $filename);

        Image::make($data['image'])
            ->fit($data['width'], $data['height'])
            ->save($storagePath);

        $imgPath = 'user-images/' . $filename;

        $userImage = auth()->user()->images()->create([
            'path' => $imgPath,
            'width' => $data['width'],
            'height' => $data['height'],
            'location' => $data['location'],
        ]);

        return new UserImageResource($userImage);
    }
}
