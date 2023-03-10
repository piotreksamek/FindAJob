<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Application;
use App\Message\AddApplicationCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddApplicationCommandHandler implements MessageHandlerInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(AddApplicationCommand $addApplicationCommand)
    {
        $application = new Application(
            $addApplicationCommand->getUser(),
            $addApplicationCommand->getOffer(),
            $addApplicationCommand->getDescription()
        );

        $this->em->persist($application);
        $this->em->flush();
    }
}