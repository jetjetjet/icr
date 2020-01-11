<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    protected $attributes = [];

    protected $hidden = ['user_password', 'createdUserName', 'createdDate', 'modifiedUserName', 'modifiedDate'];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getUserAttributes()
    {
        return $this->attributes;
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['user_id'];
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthPassword()
    {
        return $this->attributes['user_password'];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
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
