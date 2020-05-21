<?php

namespace Tests\Feature;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCommenetTest extends TestCase
{


    use RefreshDatabase;



    /** @test */
    public function user_can_comment_post()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $post = factory(Post::class)->create(['id' => 123]);

        $response = $this->post(
            '/api/posts/' . $post->id . '/comment',
            ['body' => 'test comment']
        )->assertStatus(200);

        $comment = \App\Comment::first();
        $this->assertCount(1, \App\Comment::all());

        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('test comment', $comment->body);

        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'attributes' => [
                                        'name' => $user->name,
                                        'user_id' => $user->id
                                    ]
                                ]
                            ],
                            'body' => 'test comment',
                            'commented_at' => $comment->created_at->diffForHumans()
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/123')
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts')
            ]

        ]);
    }

    /** @test */
    public function post_returns_with_likes()
    {
        $user = $this->signIn();
        $post = factory(Post::class)->create(['id' => 123, 'user_id' => $user->id]);

        $this->post('/api/posts/' . $post->id . '/like')->assertStatus(200);


        $this->get('/api/posts')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'likes' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'likes',
                                                'like_id' => 1,
                                                'attributes' => []
                                            ]
                                        ]
                                    ],
                                    'like_count' => 1,
                                    'user_likes_post' => true
                                ],
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
