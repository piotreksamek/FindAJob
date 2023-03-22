<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Company;
use Symfony\Contracts\EventDispatcher\Event;

class UserNotificationSentEvent extends Event
{
    public function __construct(private Company $sender, private string $recipient, private string $message)
    {
    }

    public function getSender(): Company
    {
        return $this->sender;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
