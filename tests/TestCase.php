<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;



    public function signIn($user = null)
    {
        if ($user == null) {
            $user = factory(User::class)->create();
        }
        $this->actingAs($user, 'api');

        return $user;
    }
}
