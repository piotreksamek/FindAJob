<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private Company $sender;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private User $recipient;

    #[ORM\Column(length: 255)]
    private string $message;

    public function __construct(Company $company, User $user, string $message)
    {
        $this->sender = $company;
        $this->recipient = $user;
        $this->message = $message;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): Company
    {
        return $this->sender;
    }

    public function getRecipient(): User
    {
        return $this->recipient;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
