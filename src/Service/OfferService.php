<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Message\AddOfferCommand;
use App\Repository\ApplicationRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OfferService
{
    public function __construct(
        private OfferRepository $offerRepository,
        private MessageBusInterface $bus,
        private EntityManagerInterface $em,
        private ApplicationRepository $applicationRepository
    ) {
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

    public function addNewOffer($createOfferRequest, $company): void
    {
        $this->bus->dispatch(new AddOfferCommand(
            $createOfferRequest->name,
            $createOfferRequest->description,
            $createOfferRequest->price ?? null,
            $createOfferRequest->city ?? null,
            $company->getId()
        ));
    }

    public function updateOffer($offer, $editOfferRequest): void
    {
        $offer->update($editOfferRequest->city, $editOfferRequest->price, $editOfferRequest->description);

        $this->em->persist($offer);
        $this->em->flush();
    }

    public function deleteOffer($offer): void
    {
        $applications = $this->applicationRepository->findBy(['owner' => $offer->getOwner()->getId()]);

        foreach ($applications as $application) {
            $this->em->remove($application);
        }

        $this->em->remove($offer);
        $this->em->flush();
    }
}
