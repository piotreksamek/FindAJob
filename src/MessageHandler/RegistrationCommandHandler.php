<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Enum\Role;
use App\Message\CreateUserCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationCommandHandler implements MessageHandlerInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher, private EntityManagerInterface $em)
    {
    }

    public function __invoke(CreateUserCommand $createUserCommand)
    {
        if ($createUserCommand->isEmployer()) {
            $role = [Role::ROLE_EMPLOYER];
        } else {
            $role = [Role::ROLE_EMPLOYEE];
        }

        $user = new User(
            $createUserCommand->getEmail(),
            $createUserCommand->getFirstName(),
            $createUserCommand->getLastName(),
            $role
        );

        $user->setPassword($this->hasher->hashPassword($user, $createUserCommand->getPassword()));

        $this->em->persist($user);
        $this->em->flush();
    }
}
