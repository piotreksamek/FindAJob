<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterFormProcessing
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function processingForm(mixed $data): User
    {
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setPassword($this->hasher->hashPassword(
                $user,
                $data['password']
            ));
            if ($data['employer']) {
                $user->setRoles(['ROLE_EMPLOYER']);
            } else {
                $user->setRoles(['ROLE_EMPLOYEE']);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
    }

    public function processingCompanyForm(mixed $data, User $user): void
    {
            $company = new Company();

            $company->setName($data['name']);
            $company->setCity($data['city']);
            $company->addUser($user);

            $user->setRoles(['ROLE_OWNER_COMPANY']);

            $this->entityManager->persist($company);
            $this->entityManager->flush();
    }
}
