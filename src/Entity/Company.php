<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(unique: true)]
    private string $name;

    #[ORM\Column(nullable: true)]
    private ?string $city;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Offer::class)]
    private Collection $offers;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private User $owner;

    #[ORM\OneToMany(mappedBy: 'Sender', targetEntity: Message::class)]
    private Collection $messages;

    public function __construct(string $name, ?string $city, User $owner)
    {
        $this->name = $name;
        $this->city = $city;
        $this->owner = $owner;
        $this->users = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            if ($user->getCompany() === $this) {
                $user->addCompany(null);
            }
        }

        return $this;
    }

    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setOwner($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->removeElement($offer)) {
            if ($offer->getOwner() === $this) {
                $offer->setOwner(null);
            }
        }

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }
}
