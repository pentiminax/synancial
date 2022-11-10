<?php

namespace App\Model\PowensApi;

class Subscription
{
    public int $id;

    public int $id_connection;

    public int $id_user;

    public int $id_source;

    public $number;

    public $label;

    public \DateTime $last_update;

    public ?Connection $connection;
}