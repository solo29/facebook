<?php

namespace Tests\Feature;

use App\UserImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('user-images');
    }

    /** @test */
    public function user_can_upload_test()
    {



        $this->withoutExceptionHandling();
        $user = $this->signIn();

        $file = UploadedFile::fake()->image('user-image.jpg');

        $response = $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'cover'
        ])->assertStatus(201);

        Storage::disk('user-images')->assertExists($file->hashName());

        $userImage = UserImage::first();

        $this->assertEquals('user-images/' . $file->hashName(), $userImage->path);
        $this->assertEquals('850', $userImage->width);
        $this->assertEquals('300', $userImage->height);
        $this->assertEquals('cover', $userImage->location);
        $this->assertEquals($userImage->user_id, $user->id);

        $response->assertJson([
            'data' => [
                'type' => 'user-images',
                'user_image_id' => $userImage->id,
                'attributes' => [
                    'path' => Storage::url($userImage->path),
                    'width' => ($userImage->width),
                    'height' => ($userImage->height),
                    'location' => ($userImage->location)
                ]
            ],
            'links' => [
                'self' => url('/users/' . $user->id)
            ]
        ]);
    }

    /** @test */
    public function users_must_return_with_images()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $file = UploadedFile::fake()->image('user-image.jpg');
        $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'cover'
        ])->assertStatus(201);

        $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 300,
            'location' => 'profile'
        ])->assertStatus(201);


        Storage::disk('user-images')->assertExists($file->hashName());

        $response = $this->get('/api/users/' . $user->id)->assertStatus(200);


        $response->assertJson([
            'data' => [
                'type' => 'users',
                'user_id' => $user->id,
                'attributes' => [
                    'name' => $user->name,
                    'cover_image' => [
                        'data' => [
                            'type' => 'user-images',
                            'user_image_id' => 1
                        ],
                    ],
                    'profile_image' => [
                        'data' => [
                            'type' => 'user-images',
                            'user_image_id' => 2
                        ],
                    ]
                ]
            ],
            'links' => ['self' => url('/users/' . $user->id)]
        ]);
    }
}
