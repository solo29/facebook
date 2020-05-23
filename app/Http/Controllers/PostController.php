<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Post;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //

    public function index()
    {
        $friends = Friend::friendships();


        if ($friends->isEmpty())
            return new PostCollection(request()->user()->posts);

        return new PostCollection(
            post::whereIn(
                'user_id',
                [
                    $friends->pluck('user_id'),
                    $friends->pluck('friend_id')
                ]
            )->get()
        );
    }

    public function store()
    {

        $data = request()->validate([
            'body' => '',
            'image' => '',
            'width' => '',
            'height' => ''
        ]);



        if (isset($data['image'])) {
            $filename = $data['image']->hashName();

            $storagePath = storage_path('app/public/post-images/' . $filename);

            if (\App::runningUnitTests())
                $storagePath = storage_path('framework/testing/disks/post-images/' . $filename);

            Image::make($data['image'])
                ->fit($data['width'], $data['height'])
                ->save($storagePath);

            $imgPath = 'post-images/' . $filename;
        }


        $post = request()->user()->posts()->create([
            'body' => $data['body'],
            'image' => $imgPath ?? null,
        ]);

        return new PostResource($post);
    }
}
