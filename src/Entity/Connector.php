<?php

namespace App\Entity;

use App\Repository\ConnectorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectorRepository::class)]
class Connector
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $uuid;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $hidden;

    #[ORM\Column(type: 'boolean')]
    private ?bool $charged;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $code;

    #[ORM\Column(type: 'boolean')]
    private ?bool $beta;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $color;

    #[ORM\Column(type: 'string', length: 4, nullable: true)]
    private ?string $slug;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $syncFrequency;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $monthsToFetch;

    #[ORM\Column(type: 'string', length: 14, nullable: true)]
    private ?string $siret;

    #[ORM\Column(type: 'boolean')]
    private ?bool $restricted;

    #[ORM\OneToMany(mappedBy: 'connector', targetEntity: Connection::class)]
    private Collection $connections;

    #[ORM\Column(type: 'json')]
    private ?array $products = [];

    public function __construct()
    {
        $this->connections = new ArrayCollection();
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
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

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(?bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isCharged(): ?bool
    {
        return $this->charged;
    }

    public function setCharged(bool $charged): self
    {
        $this->charged = $charged;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isBeta(): ?bool
    {
        return $this->beta;
    }

    public function setBeta(bool $beta): self
    {
        $this->beta = $beta;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSyncFrequency(): ?int
    {
        return $this->syncFrequency;
    }

    public function setSyncFrequency(?int $syncFrequency): self
    {
        $this->syncFrequency = $syncFrequency;

        return $this;
    }

    public function getMonthsToFetch(): ?int
    {
        return $this->monthsToFetch;
    }

    public function setMonthsToFetch(?int $monthsToFetch): self
    {
        $this->monthsToFetch = $monthsToFetch;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function isRestricted(): ?bool
    {
        return $this->restricted;
    }

    public function setRestricted(bool $restricted): self
    {
        $this->restricted = $restricted;

        return $this;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function addConnection(Connection $connection): self
    {
        if (!$this->connections->contains($connection)) {
            $this->connections[] = $connection;
            $connection->setConnector($this);
        }

        return $this;
    }

    public function removeConnection(Connection $connection): self
    {
        if ($this->connections->removeElement($connection)) {
            // set the owning side to null (unless already changed)
            if ($connection->getConnector() === $this) {
                $connection->setConnector(null);
            }
        }

        return $this;
    }

    public function getProducts(): array
    {
        return array_unique($this->products);
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }
}
