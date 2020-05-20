<?php

namespace App\Http\Controllers;

use App\Http\Resources\LikeCollection;
use App\Post;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    //

    public function store(Post $post)
    {
        auth()->user()->likedPosts()->toggle($post);

        //dd($post->likes->toArray());
        return new LikeCollection($post->likes);
    }
}
