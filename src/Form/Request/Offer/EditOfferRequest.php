<?php

declare(strict_types=1);

namespace App\Form\Request\Offer;

class EditOfferRequest extends AbstractOfferRequest
{
    public function __construct(?string $city, ?string $price, string $description)
    {
        $this->city = $city;
        $this->price = $price;
        $this->description = $description;
    }
}
