<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;

class DummyUser implements JWTSubject
{
    public $id;

    public $name;

    public $email;

    public $role;

    public function __construct(array $attributes)
    {
        $this->id = $attributes['id'] ?? null;
        $this->name = $attributes['name'] ?? null;
        $this->email = $attributes['email'] ?? null;
        $this->role = $attributes['role'] ?? null;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->email;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
