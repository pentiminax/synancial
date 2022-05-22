<?php

namespace App\Model;

class TemporaryCode
{
    public string $code;

    public string $type;

    public string $access;

    public ?int $expires_in;
}