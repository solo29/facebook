<?php

namespace Tests\Feature;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function user_can_view_profile()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();

        $response = $this->get('/api/users/' . $user->id)->assertStatus(200);

        $response->assertJson([
            'data' => [
                'type' => 'users',
                'user_id' => $user->id,
                'attributes' => [
                    'name' => $user->name
                ]
            ],
            'links' => [
                'self' => url('/users/' . $user->id)
            ]
        ]);
    }

    /** @test */
    public function user_can_view_profile_tests()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $response = $this->get('/api/users/' . $user->id . '/posts')->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'post_id' => $post->id,
                        'attributes' => [
                            'body' => $post->body,
                            'image' => $post->image,
                            'posted_at' => $post->created_at->diffForHumans(),
                            'posted_by' => [
                                'data' => [
                                    'type' => 'users',
                                    "attributes" => [
                                        "name" => $user->name
                                    ]
                                ]
                            ]
                        ],

                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }
}
