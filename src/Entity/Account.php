<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\OneToOne(inversedBy: 'account', targetEntity: Connection::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $connection;

    #[ORM\ManyToOne(targetEntity: AccountType::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    public function setConnection(Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function getType(): ?AccountType
    {
        return $this->type;
    }

    public function setType(?AccountType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
