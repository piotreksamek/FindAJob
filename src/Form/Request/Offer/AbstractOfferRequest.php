<?php

declare(strict_types=1);

namespace App\Form\Request\Offer;

abstract class AbstractOfferRequest
{
    public string $city;

    public string $price;

    public string $description;
}
