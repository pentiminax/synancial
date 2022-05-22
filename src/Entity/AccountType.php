<?php

namespace App\Entity;

use App\Repository\AccountTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountTypeRepository::class)]
class AccountType
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $name;

    #[ORM\Column(type: 'boolean')]
    private $isInvest;

    #[ORM\Column(type: 'string', length: 50)]
    private $product;

    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private $parent;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Account::class)]
    private $accounts;

    private ?int $id_parent;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isIsInvest(): ?bool
    {
        return $this->isInvest;
    }

    public function setIsInvest(bool $isInvest): self
    {
        $this->isInvest = $isInvest;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts[] = $account;
            $account->setType($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getType() === $this) {
                $account->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdParent(): ?int
    {
        return $this->id_parent;
    }

    /**
     * @param int|null $id_parent
     */
    public function setIdParent(?int $id_parent): void
    {
        $this->id_parent = $id_parent;
    }
}
