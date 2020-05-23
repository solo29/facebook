<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('post-images');
    }

    /** @test */
    public function a_user_can_post_text()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();
        $response = $this->post(
            '/api/posts',
            ['body' => 'testing body']
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


    /** @test */
    public function a_user_can_post_text_with_image()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();

        $file = UploadedFile::fake()->image('test.jpeg');

        $response = $this->post(
            '/api/posts',
            [
                'body' => 'testing body',
                'image' => $file,
                'width' => 850,
                'height' => 300,

            ]
        )->assertStatus(201);


        Storage::disk('post-images')->assertExists($file->hashName());


        $posts =  \App\Post::all();
        $this->assertCount(1, $posts);
        $post = $posts[0];





        $response->assertJson(
            [
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
                    'attributes' => [
                        'body' => 'testing body',
                        'image' => Storage::url('post-images/' . $file->hashName())

                    ]
                ],
                'links' => [
                    'self' => url('/posts/' . $post->id)
                ]

            ]
        );
    }
}
