<?php

namespace App\Model\Wallet\Savings;

use App\Model\TimestampedInterface;
use App\Model\Wallet\Checking\Account;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;

class SavingsData implements TimestampedInterface
{
    private ArrayCollection $accounts;

    private array $data;

    #[Ignore]
    private ?\DateTime $createdAt;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->data = [];
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

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}