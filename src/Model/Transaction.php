<?php

namespace App\Model;

class Transaction
{
    public int $id;

    public ?\DateTime $date;

    public ?\DateTime $datetime;

    public ?float $value;

    public string $original_wording;

    public string $simplified_wording;

    public string $stemmed_wording;

    public string $wording;
}