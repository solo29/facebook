<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_friend_request()
    {
        $this->withoutExceptionHandling();

        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();

        $response = $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $friendRequset = \App\Friend::first();

        $this->assertNotNull($friendRequset);

        $this->assertEquals($anotherUser->id, $friendRequset->friend_id);
        $this->assertEquals($user->id, $friendRequset->user_id);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequset->id,
                'attributes' => [
                    'confirmed_at' => null,
                    // 'user_id' => $user->id,
                    // 'friend_id' => $friendRequset->user_id,
                ]

            ],
            'links' => [
                'self' => url('/users/' . $anotherUser->id)
            ]
        ]);
    }

    /** @test */
    public function only_valid_users_can_be_requested()
    {


        $this->signIn();


        $this->post('/api/friend-request', [
            'friend_id' => 99999,
        ])
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'code' => 404,
                    'title' => 'User Not Found',
                    'detail' => 'Unable to locate the user with given information'
                ]
            ]);


        $this->assertNull(\App\Friend::first());
    }


    /** @test */
    public function friend_request_can_be_accepted()
    {


        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs($anotherUser)->post('/api/friend-request-response', [
            'user_id' => $user->id,
            'status' => 1,
        ])->assertStatus(200);


        $friendRequset = \App\Friend::first();

        $this->assertNotNull($friendRequset->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequset->confirmed_at);
        $this->assertEquals($friendRequset->confirmed_at, now()->startOfSecond());
        $this->assertEquals($friendRequset->status, 1);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequset->id,
                'attributes' => [
                    'confirmed_at' => $friendRequset->confirmed_at->diffForHumans(),
                    'status' => 1,
                    // 'user_id' => $user->id,
                    // 'friend_id' => $friendRequset->user_id,
                ]

            ],
            'links' => [
                'self' => url('/users/' . $anotherUser->id)
            ]
        ]);
    }
}
