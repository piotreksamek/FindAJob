<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Company;
use App\Enum\Role;
use App\Message\CreateCompanyCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateCompanyCommandHandler implements MessageHandlerInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(CreateCompanyCommand $createCompanyCommand)
    {
        $user = $createCompanyCommand->getUser();

        $company = new Company(
            $createCompanyCommand->getName(),
            $createCompanyCommand->getCity()
        );

        $company->addUser($user);
        $user->addRoles([Role::ROLE_OWNER_COMPANY]);

        $this->em->persist($company);
        $this->em->flush();
    }
}
