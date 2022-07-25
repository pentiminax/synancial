<?php

namespace App\Model\Wallet\Checking;

use App\Model\TimestampedInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;

class CheckingData implements TimestampedInterface
{
    private ArrayCollection $accounts;

    #[Ignore]
    private ?\DateTime $createdAt;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getAccounts(): ArrayCollection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): void
    {
        $this->accounts->add($account);
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}