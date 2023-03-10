<?php

declare(strict_types=1);

namespace App\Form\Request\Employer;

use App\Entity\Company;

class AddEmployerRequest
{
    public string $email;

    public Company $company;
}
