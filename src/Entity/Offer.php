<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, type: 'text')]
    private string $description;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $price ;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $dateTime;

    #[ORM\Column(length: 100, unique: true)]
    private string $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    private Company $owner;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Application::class)]
    private ?Collection $applications = null;


    public function __construct(
        string $name,
        string $description,
        ?string $price,
        ?string $city,
        Company $company
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->city = $city;
        $this->owner = $company;
        $this->slug = strtr($this->name, ' ', '-');
        $this->dateTime = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getOwner(): Company
    {
        return $this->owner;
    }

    public function update(?string $city, ?string $price, string $description): void
    {
        $this->description = $description;
        $this->city = $city;
        $this->price = $price;
    }

    public function getApplications(): ?Collection
    {
        return $this->applications;
    }

    public function addApplications(?Collection $applications): void
    {
        $this->applications = $applications;
    }
}
