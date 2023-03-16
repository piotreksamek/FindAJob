<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Builder;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class Employer implements UserBuilderInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createUser(): void
    {
        $user = new User('employer@gmail.com', 'employer', 'employer', ['ROLE_EMPLOYER']);

        $this->em->persist($user);
        $this->em->flush();
    }
}
