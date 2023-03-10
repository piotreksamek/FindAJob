<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Company;

class AddEmployerCommand
{
    public function __construct(private string $email, private Company $company)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
