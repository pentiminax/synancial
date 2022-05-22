<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;

class Distribution
{
    #[Ignore]
    private ArrayCollection $assets;

    #[Ignore]
    private ArrayCollection $liabilities;

    private Asset $checking;

    private Asset $loan;

    private Asset $market;

    private Asset $savings;

    public function __construct()
    {
        $this->checking = new Asset(0, 0);
        $this->loan = new Asset(0, 0);
        $this->market = new Asset( 0, 0);
        $this->savings = new Asset( 0, 0);

        $this->assets = new ArrayCollection([
            'checking' => $this->checking,
            'market' => $this->market,
            'savings' => $this->savings
        ]);

        $this->liabilities = new ArrayCollection([
            'loan' => $this->loan
        ]);
    }

    /**
     * @return ArrayCollection|Asset[]
     */
    public function getAssets(): ArrayCollection
    {
        return $this->assets;
    }

    public function getChecking(): Asset
    {
        return $this->checking;
    }

    public function getLiabilities(): ArrayCollection
    {
        return $this->liabilities;
    }

    public function setLiabilities(ArrayCollection $liabilities): void
    {
        $this->liabilities = $liabilities;
    }

    public function setChecking(Asset $checking): void
    {
        $this->checking = $checking;
    }

    public function getLoan(): Asset
    {
        return $this->loan;
    }

    public function setLoan(Asset $loan): void
    {
        $this->loan = $loan;
    }

    public function getMarket(): Asset
    {
        return $this->market;
    }

    public function setMarket(Asset $market): void
    {
        $this->market = $market;
    }

    public function getSavings(): Asset
    {
        return $this->savings;
    }

    public function setSavings(Asset $savings): void
    {
        $this->savings = $savings;
    }


}