<?php

namespace Tests\Feature;

use App\Friend;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RetrivePostsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function a_user_can_retrive_posts()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $posts = factory(Post::class, 2)->create(['user_id' => $user->id]);

        $response = $this->get('/api/posts')->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'post_id' => $posts->last()->id,
                        'attributes' => [
                            'body' => $posts->last()->body,
                            'image' => Storage::url($posts->last()->image),
                            'posted_at' => $posts->last()->created_at->diffForHumans()
                        ]
                    ]
                ],
                [
                    'data' => [
                        'type' => 'posts',
                        'post_id' => $posts->first()->id,
                        'attributes' => [
                            'body' => $posts->first()->body,
                            'image' => Storage::url($posts->first()->image),
                            'posted_at' => $posts->first()->created_at->diffForHumans()
                        ]
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }

    /** @test */
    public function user_can_retrive_only_own_posts()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        factory(Post::class)->create();



        $response = $this->get('/api/posts')->assertStatus(200);

        $response->assertExactJson([
            'data' => [],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }

    /** @test */
    public function a_user_can_retrive_friends_posts()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $anotherUser =  factory(User::class)->create();
        $posts = factory(Post::class, 2)->create(['user_id' => $anotherUser->id]);

        Friend::create(['user_id' => $user->id, 'friend_id' => $anotherUser->id, 'confirmed_at' => now(), 'status' => 1]);

        $response = $this->get('/api/posts')->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'post_id' => $posts->last()->id,
                        'attributes' => [
                            'body' => $posts->last()->body,
                            'image' => Storage::url($posts->last()->image),
                            'posted_at' => $posts->last()->created_at->diffForHumans()
                        ]
                    ]
                ],
                [
                    'data' => [
                        'type' => 'posts',
                        'post_id' => $posts->first()->id,
                        'attributes' => [
                            'body' => $posts->first()->body,
                            'image' => Storage::url($posts->first()->image),
                            'posted_at' => $posts->first()->created_at->diffForHumans()
                        ]
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }
}
