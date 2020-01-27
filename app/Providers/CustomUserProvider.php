<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use JWTAuth;

use Illuminate\Support\Facades\Hash;
use App\User;
use Session;
use App\Repositories\AuthRepository;

use DB;

class CustomUserProvider implements UserProvider{
    public function retrieveById($identifier)
    {
        $user = AuthRepository::get($identifier);
        return $user === null ? null : new User((array)$user);
    }

    public function retrieveByToken($identifier, $token)
    {
    }
    
    public function updateRememberToken(Authenticatable $user, $token)
    {
    }

    public function retrieveByCredentials(array $credentials)
    {
        $username =  $credentials['user_name'];
        $password =  $credentials['user_password'];
        $id = AuthRepository::authenticate($username, $password);
        if ($id === null) return null;

        $user = AuthRepository::get($id);
        return $user === null ? null : new User((array)$user);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if ($user === null) return false;
        return Hash::check($credentials['user_password'], $user->getAuthPassword());
    }
}