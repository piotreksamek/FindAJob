<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Event\UserNotificationSentEvent;
use App\Form\Request\Employer\AddEmployerRequest;
use App\Form\Request\User\CreateUserRequest;
use App\Message\CreateUserCommand;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class UserService
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private MessageBusInterface $bus)
    {
    }

    public function sendNotification(Company $company, AddEmployerRequest $addEmployeeRequest): void
    {
        $message = new UserNotificationSentEvent(
            $company,
            $addEmployeeRequest->email,
            sprintf('You have been added to a business account %s', $company->getName()
            ));

        $this->eventDispatcher->dispatch($message);
    }

    public function createUser(CreateUserRequest $createUserRequest): Envelope
    {
        return $this->bus->dispatch(new CreateUserCommand(
            $createUserRequest->email,
            $createUserRequest->firstName,
            $createUserRequest->lastName,
            $createUserRequest->password,
            $createUserRequest->isEmployer
        ));
    }
}
