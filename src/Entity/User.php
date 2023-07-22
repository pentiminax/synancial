<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email;

    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $bearerToken;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Connection::class)]
    private Collection $connections;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lastSync;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TimeSerie::class)]
    #[ORM\OrderBy(value: ['date' => 'ASC'])]
    private Collection $timeSeries;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Account::class)]
    private Collection $accounts;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isSecretModeEnabled = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Language $language = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Currency $currency = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Crowdlending::class, orphanRemoval: true)]
    private Collection $crowdlendings;

    public function __construct()
    {
        $this->connections = new ArrayCollection();
        $this->timeSeries = new ArrayCollection();
        $this->accounts = new ArrayCollection();
        $this->crowdlendings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    public function setBearerToken(?string $bearerToken): self
    {
        $this->bearerToken = $bearerToken;

        return $this;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function findConnection(int $idConnection): ?Connection
    {
        return $this->connections->filter(fn(Connection $connection) => $connection->getId() === $idConnection)->first();
    }

    public function addConnection(Connection $connection): self
    {
        if (!$this->connections->contains($connection)) {
            $this->connections[] = $connection;
            $connection->setUser($this);
        }

        return $this;
    }

    public function removeConnection(Connection $connection): self
    {
        if ($this->connections->removeElement($connection)) {
            // set the owning side to null (unless already changed)
            if ($connection->getUser() === $this) {
                $connection->setUser(null);
            }
        }

        return $this;
    }

    public function getLastSync(): ?\DateTime
    {
        return $this->lastSync;
    }

    public function setLastSync(?\DateTime $lastSync): self
    {
        $this->lastSync = $lastSync;

        return $this;
    }

    /**
     * @return Collection<int, TimeSerie>
     */
    public function getTimeSeries(?int $accountId = null): Collection
    {
        if ($accountId) {
            return $this->timeSeries->filter(fn (TimeSerie $timeSerie) => $timeSerie->getIdAccount() === $accountId);
        }

        return $this->timeSeries;
    }

    public function addTimeSeries(TimeSerie $timeSeries): self
    {
        if (!$this->timeSeries->contains($timeSeries)) {
            $this->timeSeries[] = $timeSeries;
            $timeSeries->setUser($this);
        }

        return $this;
    }

    public function removeTimeSeries(TimeSerie $timeSeries): self
    {
        if ($this->timeSeries->removeElement($timeSeries)) {
            // set the owning side to null (unless already changed)
            if ($timeSeries->getUser() === $this) {
                $timeSeries->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(?AccountType $type): Collection
    {
        if ($type) {
            return $this->accounts->filter(fn(Account $account) => $account->getType() === $type);
        }

        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts[] = $account;
            $account->setUser($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getUser() === $this) {
                $account->setUser(null);
            }
        }

        return $this;
    }

    public function isIsSecretModeEnabled(): ?bool
    {
        return $this->isSecretModeEnabled;
    }

    public function setIsSecretModeEnabled(bool $isSecretModeEnabled): self
    {
        $this->isSecretModeEnabled = $isSecretModeEnabled;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return Collection<int, Crowdlending>
     */
    public function getCrowdlendings(): Collection
    {
        return $this->crowdlendings;
    }

    public function addCrowdlending(Crowdlending $crowdlending): static
    {
        if (!$this->crowdlendings->contains($crowdlending)) {
            $this->crowdlendings->add($crowdlending);
            $crowdlending->setOwner($this);
        }

        return $this;
    }

    public function removeCrowdlending(Crowdlending $crowdlending): static
    {
        if ($this->crowdlendings->removeElement($crowdlending)) {
            // set the owning side to null (unless already changed)
            if ($crowdlending->getOwner() === $this) {
                $crowdlending->setOwner(null);
            }
        }

        return $this;
    }
}
