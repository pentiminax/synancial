<?php

namespace App\Entity;

use App\Repository\CrowdlendingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrowdlendingRepository::class)]
class Crowdlending
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $investedAmount = null;

    #[ORM\Column]
    private ?float $currentValue = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $investmentDate = null;

    #[ORM\Column(nullable: true)]
    private ?float $annualYield = null;

    #[ORM\ManyToOne(inversedBy: 'crowdlending')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CrowdlendingPlatform $platform = null;

    #[ORM\ManyToOne(inversedBy: 'crowdlendings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getInvestedAmount(): ?float
    {
        return $this->investedAmount;
    }

    public function setInvestedAmount(float $investedAmount): static
    {
        $this->investedAmount = $investedAmount;

        return $this;
    }

    public function getCurrentValue(): ?float
    {
        return $this->currentValue;
    }

    public function setCurrentValue(float $currentValue): static
    {
        $this->currentValue = $currentValue;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getInvestmentDate(): ?\DateTimeInterface
    {
        return $this->investmentDate;
    }

    public function setInvestmentDate(?\DateTimeInterface $investmentDate): static
    {
        $this->investmentDate = $investmentDate;

        return $this;
    }

    public function getAnnualYield(): ?float
    {
        return $this->annualYield;
    }

    public function setAnnualYield(?float $annualYield): static
    {
        $this->annualYield = $annualYield;

        return $this;
    }

    public function getPlatform(): ?CrowdlendingPlatform
    {
        return $this->platform;
    }

    public function setPlatform(?CrowdlendingPlatform $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
