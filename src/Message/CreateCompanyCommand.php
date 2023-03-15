<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\User;

class CreateCompanyCommand
{
    private string $name;

    private ?string $city;

    private User $user;

    public function __construct(string $name, ?string $city, User $user)
    {
        $this->name = $name;
        $this->city = $city;
        $this->user = $user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
