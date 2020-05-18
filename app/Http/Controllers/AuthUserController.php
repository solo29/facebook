<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function show()
    {
        return new UserResource(auth()->user());
    }
}
