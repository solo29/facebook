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
            $imgPath = $data['image']->store('post-images', 'public');
            if (!\App::runningUnitTests()) {
                //this is resizing image and overriding it
                Image::make($data['image'])
                    ->fit($data['width'], $data['height'])
                    ->save(storage_path('app/public/post-images/' . $data['image']->hashName()));
            }
        }


        $post = request()->user()->posts()->create([
            'body' => $data['body'],
            'image' => $imgPath ?? null,
        ]);

        return new PostResource($post);
    }
}
