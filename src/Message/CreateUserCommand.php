<?php

declare(strict_types=1);

namespace App\Message;

class CreateUserCommand
{
    private string $email;

    private string $firstName;

    private string $lastName;

    private string $password;

    private bool $isEmployer;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $password,
        bool $isEmployer
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
        $this->isEmployer = $isEmployer;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isEmployer(): bool
    {
        return $this->isEmployer;
    }
}
