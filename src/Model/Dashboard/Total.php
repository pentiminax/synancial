<?php

namespace App\Model\Dashboard;

class Total
{
    private float $amount;

    private float $netWorth;

    private float $financialAssets;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getNetWorth(): float
    {
        return $this->netWorth;
    }

    public function setNetWorth(float $netWorth): void
    {
        $this->netWorth = $netWorth;
    }

    public function getFinancialAssets(): float
    {
        return $this->financialAssets;
    }

    public function setFinancialAssets(float $financialAssets): void
    {
        $this->financialAssets = $financialAssets;
    }
}