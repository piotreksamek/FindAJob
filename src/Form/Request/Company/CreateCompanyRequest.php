<?php

declare(strict_types=1);

namespace App\Form\Request\Company;

class CreateCompanyRequest
{
    public string $name;

    public ?string $city;
}
