<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Builder;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserBuilder implements UserBuilderInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher, private EntityManagerInterface $entityManager)
    {
    }

    private function createUser(
        ?string $email,
        ?string $firstName,
        ?string $lastName,
        ?array $roles,
        ?string $password
    ): User
    {
        $user = new User($email, $firstName, $lastName, $roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function createEmployer(): User
    {
        return $this->createUser(
            'employer@gmail.com',
            'employer',
            'employer',
            [Role::ROLE_EMPLOYER],
            'employer'
        );
    }

    public function createEmployee(): User
    {
        return $this->createUser(
            'employee@gmail.com',
            'employee',
            'employee',
            [Role::ROLE_EMPLOYEE],
            'employee'
        );
    }

    public function createCompanyOwner(): User
    {
        $user = $this->createUser(
            'company.owner@gmail.com',
            'company',
            'owner',
            [Role::ROLE_EMPLOYER],
            'companyowner'
        );

        $company = new Company('Facebook', 'Warsaw' , $user);

        $user->addRoles([Role::ROLE_OWNER_COMPANY]);
        $user->addCompany($company);

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $user;
    }
}
