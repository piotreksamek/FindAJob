<?php

declare(strict_types=1);

namespace App\Api\Service;

use App\Entity\Company;
use App\Form\Request\Offer\CreateOfferRequest;
use App\Service\OfferService;

class ApiOfferService
{
    public function __construct(private OfferService $offerService)
    {
    }

    public function addOffer(array $data, int $companyId): void
    {
        if(!$this->validateOffer($data)){

            throw new \JsonException('Cant add offer without name or description');
        }

        /** @var Company $company */
        $createOfferRequest = new CreateOfferRequest();
        $createOfferRequest->name = $data['name'];
        $createOfferRequest->description = $data['description'];
        $createOfferRequest->city = $data['city'] ?? null;
        $createOfferRequest->price = $data['price'] ?? null;

        $this->offerService->addNewOffer($createOfferRequest, $companyId);
    }

    private function validateOffer(array $data): bool
    {
        if ($data['name'] === "" || $data['description'] === "") {

            return false;
        }

        return true;
    }
}