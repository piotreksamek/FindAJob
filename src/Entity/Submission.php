<?php

declare(strict_types=1);

namespace App\Entity;

class Submission
{
    private string $id;

    private string $owner;

    private string $offer;

    private string $description;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): void
    {
        $this->owner = $owner;
    }

    public function getOffer(): string
    {
        return $this->offer;
    }

    public function setOffer(string $offer): void
    {
        $this->offer = $offer;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
