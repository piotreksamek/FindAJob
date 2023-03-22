<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessagesSender
{
    public function __construct(private MessageRepository $messageRepository, private EntityManagerInterface $em)
    {
    }

    public function sendMessage(int $id): ?string
    {
        $messages = $this->messageRepository->findBy(['recipient' => $id]);

        if(!$messages){
            return null;
        }

        foreach ($messages as $message) {
            $description = $message->getMessage();
        }

        $this->em->remove($message);
        $this->em->flush();

        return $description;
    }
}
