<?php

namespace App\Entity;

use App\Repository\CrowdlendingPlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrowdlendingPlatformRepository::class)]
class CrowdlendingPlatform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'platform', targetEntity: Crowdlending::class, orphanRemoval: true)]
    private Collection $crowdlending;

    public function __construct()
    {
        $this->crowdlending = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Crowdlending>
     */
    public function getCrowdlending(): Collection
    {
        return $this->crowdlending;
    }

    public function addCrowdlending(Crowdlending $crowdlending): static
    {
        if (!$this->crowdlending->contains($crowdlending)) {
            $this->crowdlending->add($crowdlending);
            $crowdlending->setPlatform($this);
        }

        return $this;
    }

    public function removeCrowdlending(Crowdlending $crowdlending): static
    {
        if ($this->crowdlending->removeElement($crowdlending)) {
            // set the owning side to null (unless already changed)
            if ($crowdlending->getPlatform() === $this) {
                $crowdlending->setPlatform(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
