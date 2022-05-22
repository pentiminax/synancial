<?php

namespace App\Entity;

use App\Repository\TimeSerieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeSerieRepository::class)]
class TimeSerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'timeSeries')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'integer')]
    private $idAccount;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $value;

    #[ORM\Column(type: 'date')]
    private $date;

    public function __construct(int $accountId, float $value, \DateTime $date, User $user)
    {
        $this->idAccount = $accountId;
        $this->value = $value;
        $this->date = $date;
        $this->user = $user;
    }

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

    public function getIdAccount(): ?int
    {
        return $this->idAccount;
    }

    public function setIdAccount(int $idAccount): self
    {
        $this->idAccount = $idAccount;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
