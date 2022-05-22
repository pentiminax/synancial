<?php

namespace App\Entity;

use App\Repository\ConnectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
class Connection
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Connector::class, inversedBy: 'connections')]
    #[ORM\JoinColumn(nullable: false)]
    private $connector;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastUpdate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'connections')]
    private $user;

    #[ORM\OneToOne(mappedBy: 'connection', targetEntity: Account::class, cascade: ['persist', 'remove'])]
    private $account;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConnector(): ?Connector
    {
        return $this->connector;
    }

    public function setConnector(?Connector $connector): self
    {
        $this->connector = $connector;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        // set the owning side of the relation if necessary
        if ($account->getConnection() !== $this) {
            $account->setConnection($this);
        }

        $this->account = $account;

        return $this;
    }
}
