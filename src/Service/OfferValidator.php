<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\OfferRepository;

class OfferValidator
{
    public function __construct(private OfferRepository $offerRepository)
    {
    }

    public function validateNewOffer(Company $company, string $offerName): bool
    {
        $offers = $this->offerRepository->findBy(['name' => $offerName]);
        foreach ($offers as $offer) {
            if ($offer->getOwner() === $company) {

                return false;
            }
        }

        return true;
    }
}
