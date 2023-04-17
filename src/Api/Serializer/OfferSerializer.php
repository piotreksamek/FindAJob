<?php

declare(strict_types=1);

namespace App\Api\Serializer;

use App\Api\ResponseModel\OfferResponseModel;
use App\Entity\Offer;
use Symfony\Component\Serializer\SerializerInterface;

class OfferSerializer
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function serializeOffers(array $offers): array
    {
        $result = [];

        foreach ($offers as $offer) {
            $result[] = $this->serialize($offer);
        }

        return $result;
    }

    public function serializeOffer(Offer $offer): array
    {
        return $this->serialize($offer);
    }

    private function serialize(Offer $offer): array
    {
        $offerResponseModel = new OfferResponseModel(
            $offer->getId(),
            $offer->getName(),
            $offer->getDescription(),
            $offer->getPrice(),
            $offer->getDescription(),
            $offer->getOwner()->getName()
        );

        return $this->serializer->normalize($offerResponseModel);
    }
}
