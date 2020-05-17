<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_post_text()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();
        $response = $this->post(
            '/api/posts',
            [
                'data' => [
                    'type' => 'posts',
                    'attributes' => [
                        'body' => 'testing body',
                    ]
                ]
            ]
        )->assertStatus(201);

        $posts =  \App\Post::all();
        $this->assertCount(1, $posts);
        $post = $posts[0];
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('testing body', $post->body);

        $response->assertJson(
            [
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
                    'attributes' => [
                        'posted_by' => [
                            'data' => [
                                'attributes' => [
                                    'name' => $user->name
                                ]
                            ]
                        ],
                        'body' => 'testing body'
                    ]
                ],
                'links' => [
                    'self' => url('/posts/' . $post->id)
                ]

            ]
        );
    }
}
