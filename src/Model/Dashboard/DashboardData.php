<?php

namespace App\Model\Dashboard;

use App\Model\Asset;
use App\Model\Distribution;
use App\Model\TimestampedInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

class DashboardData implements TimestampedInterface
{
    private Distribution $distribution;

    private Total $total;

    #[Ignore]
    private ?\DateTime $createdAt;

    public function __construct()
    {
        $this->distribution = new Distribution();
        $this->total = new Total();
        $this->createdAt = new \DateTime();
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function setDistribution(Distribution $distribution): self
    {
        $this->distribution = $distribution;

        return $this;
    }

    public function getTotal(): Total
    {
        return $this->total;
    }

    public function setTotal(Total $total): void
    {
        $this->total = $total;
    }


    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function processCalculatedData(): void
    {
        $assets = $this->distribution->getAssets();
        $checking = $this->distribution->getChecking();
        $loan = $this->distribution->getLoan();
        $market = $this->distribution->getMarket();
        $savings = $this->distribution->getSavings();

        $amount = array_sum($assets
            ->filter(fn(Asset $asset) => $asset !== $loan)
            ->map(fn(Asset $asset) => $asset->getAmount())->toArray());

        $netWorth = $amount - $loan->getAmount();
        $financialAssets = $market->getAmount() + $savings->getAmount();

        $this->total->setAmount($amount);
        $this->total->setNetWorth($netWorth);
        $this->total->setFinancialAssets($financialAssets);

        $totalAmount = $this->total->getAmount();

        if (0.0 === $totalAmount) {
            return;
        }

        $checking->setShare($checking->getAmount() / $totalAmount * 100);
        $market->setShare($market->getAmount() / $totalAmount * 100);
        $savings->setShare($savings->getAmount() / $totalAmount * 100);
    }
}