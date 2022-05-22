<?php

namespace App\Model;

class Asset
{
    private ?float $amount;

    private ?float $share;

    public function __construct(?float $amount = null, ?float $share = null)
    {
        $this->amount = $amount;
        $this->share = $share;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    public function addAmount(float $amount)
    {
        $this->amount += $amount;
    }

    public function getShare(): ?float
    {
        return $this->share;
    }

    public function setShare(?float $share): void
    {
        $this->share = $share;
    }
}