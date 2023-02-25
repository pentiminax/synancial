<?php

namespace App\Model\Dashboard;

use App\Model\Asset;
use App\Model\DataInterface;
use App\Model\Distribution;
use App\Model\TimestampedInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

class DashboardData implements DataInterface, TimestampedInterface
{
    private AllocationChart $allocationChart;

    #[Ignore]
    private ?\DateTime $createdAt;

    private Distribution $distribution;

    private Total $total;

    public function __construct()
    {
        $this->allocationChart = new AllocationChart();
        $this->createdAt = new \DateTime();
        $this->distribution = new Distribution();
        $this->total = new Total();
    }

    public function getAllocationChart(): AllocationChart
    {
        return $this->allocationChart;
    }

    public function setAllocationChart(AllocationChart $allocationChart): self
    {
        $this->allocationChart = $allocationChart;

        return $this;
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
        $checking = $this->distribution->getChecking();
        $crowdlendings = $this->distribution->getCrowdlendings();
        $loan = $this->distribution->getLoan();
        $market = $this->distribution->getMarket();
        $savings = $this->distribution->getSavings();

        $amount = array_sum($this->distribution->getAssets()
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
        $crowdlendings->setShare($crowdlendings->getAmount() / $totalAmount * 100);
        $market->setShare($market->getAmount() / $totalAmount * 100);
        $savings->setShare($savings->getAmount() / $totalAmount * 100);
    }
}