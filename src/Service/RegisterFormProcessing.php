<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\Role;
use App\Form\Request\Company\CreateCompanyRequest;
use App\Form\Request\User\CreateUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterFormProcessing
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function processingForm(CreateUserRequest $createUserRequest): User
    {
            $user = new User();
            $user->setFirstName($createUserRequest->firstName);
            $user->setLastName($createUserRequest->lastName);
            $user->setEmail($createUserRequest->email);
            $user->setPassword($this->hasher->hashPassword(
                $user,
                $createUserRequest->password
            ));
            if ($createUserRequest->isEmployer) {
                $user->setRoles([Role::ROLE_EMPLOYER]);
            } else {
                $user->setRoles([Role::ROLE_EMPLOYEE]);
            }

            $this->em->persist($user);
            $this->em->flush();

            return $user;
    }

    public function processingCompanyForm(CreateCompanyRequest $createCompanyRequest, User $user): void
    {
            $company = new Company();

            $company->setName($createCompanyRequest->name);
            $company->setCity($createCompanyRequest->city);
            $company->addUser($user);

            $user->setRoles([Role::ROLE_OWNER_COMPANY]);

            $this->em->persist($company);
            $this->em->flush();
    }
}
