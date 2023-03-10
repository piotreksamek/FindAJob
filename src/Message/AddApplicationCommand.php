<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Offer;
use App\Entity\User;

class AddApplicationCommand
{
    public function __construct(private User $user, private Offer $offer, private string $description)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOffer(): Offer
    {
        return $this->offer;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
