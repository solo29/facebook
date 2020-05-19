<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {

    $imgUrls = [
        'https://images.unsplash.com/photo-1588795398198-252ce16a029b?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=698&ixlib=rb-1.2.1&q=80&w=800',
        'https://images.unsplash.com/photo-1588776874042-0fbf808e29c8?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=681&ixlib=rb-1.2.1&q=80&w=800',
        'https://images.unsplash.com/photo-1587356323024-67118f87c07c?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=610&ixlib=rb-1.2.1&q=80&w=800',
        'https://images.unsplash.com/photo-1589050590928-b7a42c4e3f12?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=685&ixlib=rb-1.2.1&q=80&w=800',
        'https://images.unsplash.com/photo-1588977617373-88d29fb4f2c2?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=600&ixlib=rb-1.2.1&q=80&w=800',
    ];

    return [
        'body' => $faker->sentence,
        'user_id' => factory(App\User::class),
        'image' => $imgUrls[rand(0, 4)]
    ];
});
