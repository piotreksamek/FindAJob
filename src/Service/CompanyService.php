<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Form\Request\Company\CreateCompanyRequest;
use App\Message\CreateCompanyCommand;
use Symfony\Component\Messenger\MessageBusInterface;

class CompanyService
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function createCompany(CreateCompanyRequest $createCompanyRequest, User $user)
    {
        $this->bus->dispatch(new CreateCompanyCommand(
            $createCompanyRequest->name,
            $createCompanyRequest->city ?? null,
            $user
        ));
    }
}
