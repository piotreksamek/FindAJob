<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterFormProcessing
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function processingForm(mixed $data): void
    {
        $user = new User();

        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPassword($this->hasher->hashPassword(
            $user,
            $data['password']
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}