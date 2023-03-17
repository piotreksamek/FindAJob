<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Builder;

use App\Entity\User;

interface UserBuilderInterface
{
    public function createEmployer(): User;

    public function createEmployee(): User;

    public function createCompanyOwner(): User;
}
