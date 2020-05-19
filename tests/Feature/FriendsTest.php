<?php

namespace Tests\Feature;

use App\Friend;
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
    public function unique_friends_request()
    {
        $this->withoutExceptionHandling();

        $this->signIn();
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $this->assertEquals(1, \App\Friend::count());
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
                    'user_id' => $user->id,
                    'friend_id' => $friendRequset->user_id,
                ]

            ],
            'links' => [
                'self' => url('/users/' . $anotherUser->id)
            ]
        ]);
    }

    /** @test */
    public function only_valid_friend_requests_can_be_accepted()
    {


        $user = $this->signIn();
        $response = $this->post('/api/friend-request-response', [
            'user_id' => 9999,
            'status' => 1,
        ])->assertStatus(404);

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate friend request with given information'
            ]
        ]);
    }


    /** @test */
    public function only_recepitians_can_accept_request()
    {


        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();
        $thirdUser =  factory(User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs($thirdUser)->post('/api/friend-request-response', [
            'user_id' => $user->id,
            'status' => 1,
        ])->assertStatus(404);


        $friendRequset = \App\Friend::first();

        $this->assertNull($friendRequset->confirmed_at);
        $this->assertNull($friendRequset->status);


        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate friend request with given information'
            ]
        ]);
    }


    /** @test */
    public function friend_id_is_required_for_friend_request()
    {
        $user = $this->signIn();

        $response = $this->post('/api/friend-request', [
            'friend_id' => '',
        ])->assertStatus(422)
            ->assertJsonStructure(
                [
                    'errors' => [
                        'meta'
                    ]
                ]
            );
    }

    /** @test */
    public function friend_request_response_user_id_and_status_required()
    {
        $this->signIn();
        $this->post('/api/friend-request-response', [
            'user_id' => '',
            'status' => '',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'meta' =>
                    [
                        'user_id',
                        'status'
                    ]
                ]
            ]);

        // this is some to assertJsonStructure
        // $responseJson = json_decode($response->getContent(), true);

        // $this->assertArrayHasKey('user_id', $responseJson['errors']['meta']);
        // $this->assertArrayHasKey('status', $responseJson['errors']['meta']);
    }

    /** @test */
    public function friendship_is_retrieved_when_fetching_profile()
    {
        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();

        $friendRequset = \App\Friend::create([
            'user_id' => $user->id,
            'friend_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1
        ]);

        $this->withoutExceptionHandling();

        $this->get('/api/users/' . $anotherUser->id)->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'name' => $anotherUser->name,
                        'friendship' => [
                            'data' =>
                            [
                                'friend_request_id' => $friendRequset->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }


    /** @test */
    public function inverse_friendship_is_retrieved_when_fetching_profile()
    {
        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();

        $friendRequset = \App\Friend::create([
            'friend_id' => $user->id,
            'user_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1
        ]);

        $this->withoutExceptionHandling();

        $this->get('/api/users/' . $anotherUser->id)->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'name' => $anotherUser->name,
                        'friendship' => [
                            'data' =>
                            [
                                'friend_request_id' => $friendRequset->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }



    /** @test */
    public function friend_request_can_be_rejected()
    {

        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs($anotherUser)->delete('/api/friend-request-response/delete', [
            'user_id' => $user->id,
        ])->assertStatus(204);

        $response->assertNoContent();

        $friendRequset = \App\Friend::first();

        $this->assertNull($friendRequset);
    }



    /** @test */
    public function only_recepitians_can_reject_request()
    {


        $user = $this->signIn();
        $anotherUser = factory(User::class)->create();
        $thirdUser =  factory(User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs($thirdUser)->delete('/api/friend-request-response/delete', [
            'user_id' => $user->id,
            'status' => 1,
        ])->assertStatus(404);


        $friendRequset = \App\Friend::first();

        $this->assertNull($friendRequset->confirmed_at);
        $this->assertNull($friendRequset->status);


        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate friend request with given information'
            ]
        ]);
    }



    /** @test */
    public function user_id_is_required_for_friend_request_delete()
    {

        $user = $this->signIn();

        $response = $this->delete('/api/friend-request-response/delete', [
            'user_id' => '',
        ])->assertStatus(422)
            ->assertJsonStructure(
                [
                    'errors' => [
                        'meta' => [
                            'user_id'
                        ]
                    ]
                ]
            );
    }
}
