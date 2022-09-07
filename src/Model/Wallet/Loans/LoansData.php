<?php

namespace App\Model\Wallet\Loans;

use App\Model\TimestampedInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

class LoansData  implements TimestampedInterface
{
    private array $data;

    #[Ignore]
    private ?\DateTime $createdAt;

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->data = [];
    }
}