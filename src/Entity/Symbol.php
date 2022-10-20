<?php

namespace App\Entity;

use App\Repository\SymbolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SymbolRepository::class)]
class Symbol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $symbol = null;

    #[ORM\Column(length: 255)]
    private ?string $isin = null;

    #[ORM\Column(length: 255)]
    private ?string $dividendFrequency = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dividendCurrency = null;

    #[ORM\OneToMany(mappedBy: 'symbol', targetEntity: Dividend::class)]
    private Collection $dividends;

    public function __construct()
    {
        $this->dividends = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getIsin(): ?string
    {
        return $this->isin;
    }

    public function setIsin(string $isin): self
    {
        $this->isin = $isin;

        return $this;
    }

    public function getDividendFrequency(): ?string
    {
        return $this->dividendFrequency;
    }

    public function setDividendFrequency(string $dividendFrequency): self
    {
        $this->dividendFrequency = $dividendFrequency;

        return $this;
    }

    public function getDividendCurrency(): ?string
    {
        return $this->dividendCurrency;
    }

    public function setDividendCurrency(string $dividendCurrency): self
    {
        $this->dividendCurrency = $dividendCurrency;

        return $this;
    }

    /**
     * @return Collection<int, Dividend>
     */
    public function getDividends(): Collection
    {
        return $this->dividends;
    }

    public function addDividend(Dividend $dividend): self
    {
        if (!$this->dividends->contains($dividend)) {
            $this->dividends->add($dividend);
            $dividend->setSymbol($this);
        }

        return $this;
    }

    public function removeDividend(Dividend $dividend): self
    {
        if ($this->dividends->removeElement($dividend)) {
            // set the owning side to null (unless already changed)
            if ($dividend->getSymbol() === $this) {
                $dividend->setSymbol(null);
            }
        }

        return $this;
    }
}
