<?php

declare(strict_types=1);

namespace App\Form\Request\User;

class CreateUserRequest
{
    public string $email;

    public string $firstName;

    public string $lastName;

    public string $password;

    public bool $isEmployer;
}
