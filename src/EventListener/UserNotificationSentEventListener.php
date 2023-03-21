<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Message;
use App\Event\UserNotificationSentEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserNotificationSentEventListener
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $em)
    {
    }

    public function onAddedEmployee(UserNotificationSentEvent $event): void
    {
        $sender = $event->getSender();
        $recipientEmail = $event->getRecipient();
        $message = $event->getMessage();

        $recipient = $this->userRepository->findUserByEmail($recipientEmail);

        $alert = new Message($sender, $recipient, $message);

        $this->em->persist($alert);
        $this->em->flush();
    }
}
