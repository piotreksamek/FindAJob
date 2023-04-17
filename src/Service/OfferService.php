<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\Offer;
use App\Form\Request\Offer\CreateOfferRequest;
use App\Form\Request\Offer\EditOfferRequest;
use App\Message\AddOfferCommand;
use App\Repository\ApplicationRepository;
use App\Repository\CompanyRepository;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OfferService
{
    public function __construct(
        private OfferRepository $offerRepository,
        private MessageBusInterface $bus,
        private EntityManagerInterface $em,
        private ApplicationRepository $applicationRepository,
        private CompanyRepository $companyRepository
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

    public function addNewOffer(CreateOfferRequest $createOfferRequest, int $companyId): void
    {
        $this->bus->dispatch(new AddOfferCommand(
            $createOfferRequest->name,
            $createOfferRequest->description,
            $createOfferRequest->price ?? null,
            $createOfferRequest->city ?? null,
            $companyId
        ));
    }

    public function updateOffer(Offer $offer, EditOfferRequest $editOfferRequest): void
    {
        $offer->update($editOfferRequest->city, $editOfferRequest->price, $editOfferRequest->description);

        $this->em->persist($offer);
        $this->em->flush();
    }

    public function deleteOffer(Offer $offer): void
    {
        $applications = $this->applicationRepository->findBy(['owner' => $offer->getOwner()->getId()]);

        foreach ($applications as $application) {
            $this->em->remove($application);
        }

        $this->em->remove($offer);
        $this->em->flush();
    }

    public function canAddOffer(int $companyId): bool
    {
        $company = $this->companyRepository->findOneBy(['id' => $companyId]);

        $offers = $company->getOffers();

        if (count($offers->toArray()) === 3) {

            return false;
        }

        return true;
    }
}
