<?php

namespace App\Model;

class UserAccessToken
{
    public string $auth_token;

    public string $type;

    public int $id_user;

    public ?int $expires_in;
}