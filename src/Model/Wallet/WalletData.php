<?php

namespace App\Model\Wallet;

use App\Model\Asset;
use App\Model\Distribution;
use App\Model\TimestampedInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

class WalletData implements TimestampedInterface
{
    private Distribution $distribution;

    #[Ignore]
    private ?\DateTime $createdAt;

    public function __construct()
    {
        $this->distribution = new Distribution();
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

        $totalAmount = array_sum($assets->map(fn(Asset $asset) => $asset->getAmount())->toArray());

        if (0.0 === $totalAmount) {
            return;
        }

        foreach($assets as $asset) {
            $asset->setShare($asset->getAmount() / $totalAmount * 100);
        }
    }
}