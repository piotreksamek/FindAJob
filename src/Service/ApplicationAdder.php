<?php

declare(strict_types=1);

namespace App\Service;

use App\Message\AddApplicationCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class ApplicationAdder
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function addApplication($user, $offer, $description): bool
    {
        foreach ($user->getApplications() as $application) {
            if ($application->getOffer() === $offer) {

                return false;
            }
        }

        $this->bus->dispatch(new AddApplicationCommand($user,
            $offer,
            $description
        ));

        return true;
    }
}
