<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Enum\Role;
use App\Message\AddEmployerCommand;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddEmployerCommandHandler implements MessageHandlerInterface
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $em)
    {
    }

    public function __invoke(AddEmployerCommand $addEmployerCommand): void
    {
        $company = $addEmployerCommand->getCompany();
        $email = $addEmployerCommand->getEmail();

        $employer = $this->userRepository->findUserByEmail($email);

        if (!$employer->hasRoles(Role::ROLE_EMPLOYER)) {

            throw new \Exception('User is not Employer');
        } elseif ($employer->getCompany() !== null) {

            throw new \Exception('User has company');
        }
        $employer->setCompany($company);

        $this->em->persist($employer);
        $this->em->flush();
    }
}
