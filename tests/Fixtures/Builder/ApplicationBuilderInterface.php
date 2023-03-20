<?php

namespace App\Tests\Fixtures\Builder;

use App\Entity\Application;

interface ApplicationBuilderInterface
{
    public function createApplication(): Application;
}
