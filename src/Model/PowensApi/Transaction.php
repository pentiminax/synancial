<?php

namespace App\Model\PowensApi;

class Transaction
{
    public int $id;

    public int $id_account;

    public ?\DateTime $date;

    public ?\DateTime $datetime;

    public ?float $value;

    public string $type;

    public ?float $gross_value;

    public string $original_wording;

    public string $simplified_wording;

    public string $stemmed_wording;

    public string $wording;

    public bool $coming;

    public ?float $commission;

    public ?string $country;

    public ?string $card;
}