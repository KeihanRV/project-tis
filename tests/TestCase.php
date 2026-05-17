<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    /**
     * Helper to authenticate a user using JWT and attach Authorization header.
     *
     * Usage: $this->actingWithToken($user)->getJson(...)
     */
    public function actingWithToken(User $user)
    {
        $token = JWTAuth::fromUser($user);

        return $this->withHeader('Authorization', "Bearer {$token}");
    }
}
