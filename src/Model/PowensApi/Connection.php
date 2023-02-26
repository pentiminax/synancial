<?php

namespace App\Model\PowensApi;

class Connection
{
    public int $id;

    public int $id_user;

    public int $id_connector;

    public ?\DateTime $last_update;

    public \DateTime $created;

    public bool $active;

    public ?string $state;

    public ?string $error;

    public int $id_bank;

    public string $connector_uuid;
}