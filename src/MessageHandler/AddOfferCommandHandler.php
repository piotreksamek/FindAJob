<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Company;
use App\Entity\Offer;
use App\Message\AddOfferCommand;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddOfferCommandHandler implements MessageHandlerInterface
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function __invoke(AddOfferCommand $addOfferCommand)
    {
        /** @var Company $company */
        $company = $this->companyRepository->find($addOfferCommand->getCompanyId());

        $offer = new Offer(
            $addOfferCommand->getName(),
            $addOfferCommand->getDescription(),
            $addOfferCommand->getPrice(),
            $addOfferCommand->getCity(),
            $company
        );

        $this->em->persist($offer);
        $this->em->flush();
    }
}
