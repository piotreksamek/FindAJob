<?php

namespace App\Tests\Fixtures\Builder;

use App\Entity\Offer;

interface OfferBuilderInterface
{
    public function createOffer(): Offer;
}
