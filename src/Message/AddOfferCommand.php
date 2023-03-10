<?php

declare(strict_types=1);

namespace App\Message;

class AddOfferCommand
{
    public function __construct(
        private string $name,
        private string $description,
        private string $price,
        private string $city,
        private int $companyId
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }
}
