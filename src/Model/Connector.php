<?php

namespace App\Model;

class Connector
{
    /**
     * @var int
     * ID of the connector, not stable across API domains.
     */
    public int $id;

    /**
     * @var string
     * Unique connector identifier, stable across API domains.
     */
    public string $uuid;

    /**
     * @var string
     * 	Name of the bank or provide.
     */
    public string $name;

    /**
     * @var bool|null
     * 	Whether this connector is hidden from users.
     */
    public ?bool $hidden;

    public bool $charged;

    public ?string $code;

    public bool $beta;

    public ?string $color;

    public ?string $slug;

    public ?int $sync_frequency;
}