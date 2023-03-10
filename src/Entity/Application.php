<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'application')]
    private User $owner;

    #[ORM\ManyToOne(inversedBy: 'application')]
    private Offer $offer;

    #[ORM\Column(length: 255, type: 'text')]
    private string $description;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct(User $owner, Offer $offer, string $description)
    {
        $this->owner = $owner;
        $this->offer = $offer;
        $this->description = $description;
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getOffer(): Offer
    {
        return $this->offer;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i');
    }
}
