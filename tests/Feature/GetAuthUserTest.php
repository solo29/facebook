<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAuthUserTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function auth_user_can_be_fetched()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();


        $response = $this->get('/api/auth-user')->assertStatus(200);

        $response->assertJson([
            'data' => [
                'user_id' => $user->id,
                'attributes' => [
                    'name' => $user->name,

                ]
            ],
            'links' => ['self' => url('/users/' . $user->id)]
        ]);
    }
}
