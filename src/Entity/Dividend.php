<?php

namespace App\Entity;

use App\Repository\DividendRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DividendRepository::class)]
class Dividend
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $exDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $payDate = null;

    #[ORM\Column(nullable: true)]
    private ?float $amount = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $currency = null;

    #[ORM\ManyToOne(inversedBy: 'dividends')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Symbol $symbol = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExDate(): ?\DateTimeInterface
    {
        return $this->exDate;
    }

    public function setExDate(?\DateTimeInterface $exDate): self
    {
        $this->exDate = $exDate;

        return $this;
    }

    public function getPayDate(): ?\DateTimeInterface
    {
        return $this->payDate;
    }

    public function setPayDate(?\DateTimeInterface $payDate): self
    {
        $this->payDate = $payDate;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSymbol(): ?Symbol
    {
        return $this->symbol;
    }

    public function setSymbol(?Symbol $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }
}
