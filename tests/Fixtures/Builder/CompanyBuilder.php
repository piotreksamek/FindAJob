<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Builder;

use App\Entity\Company;

class CompanyBuilder
{
    public function __construct(private UserBuilder $userBuilder)
    {
    }

    public function createCompany(): Company
    {
        $user = $this->userBuilder->createCompanyOwner();
        $company = new Company('Facebook', 'Warszawa', $user);

        return $company;
    }
}