<?php

namespace App\Model;

class Investment
{
    const CODE_TYPE_ISIN = 'ISIN';

    public int $id;

    public string $label;

    public string $code;

    public ?string $code_type;

    public ?float $quantity;

    public ?float $unitprice;

    public ?float $unitvalue;

    public float $valuation;

    public ?float $prev_diff;

    public ?float $prev_diff_percent;
}