<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Builder;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class ApplicationBuilder implements ApplicationBuilderInterface
{
    public function __construct(
        private OfferBuilder $offerBuilder,
        private UserBuilder $userBuilder,
        private EntityManagerInterface $em,
    ) {
    }

    public function createApplication(): Application
    {
        $user = $this->userBuilder->createEmployee();
        $offer = $this->offerBuilder->createOffer();

        $application = new Application($user, $offer, 'TEST');

        $this->em->persist($application);
        $this->em->flush();

        return $application;
    }
}
