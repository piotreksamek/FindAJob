<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterFormProcessing
{
    public function __construct(
        private EntityManagerInterface $em,
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
                $user->setRoles([Role::ROLE_EMPLOYER]);
            } else {
                $user->setRoles([Role::ROLE_EMPLOYEE]);
            }

            $this->em->persist($user);
            $this->em->flush();

            return $user;
    }

    public function processingCompanyForm(mixed $data, User $user): void
    {
            $company = new Company();

            $company->setName($data['name']);
            $company->setCity($data['city']);
            $company->addUser($user);

            $user->setRoles(['ROLE_OWNER_COMPANY']);

            $this->em->persist($company);
            $this->em->flush();
    }
}
