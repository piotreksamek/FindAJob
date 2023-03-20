<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\Builder;

use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;

class OfferBuilder implements OfferBuilderInterface
{
    public function __construct(private UserBuilder $userBuilder, private EntityManagerInterface $em)
    {
    }

    public function createOffer(): Offer
    {
        $user = $this->userBuilder->createCompanyOwner();
        $offer = new Offer(
            'Senior Developer',
            'ASDASD' ,
            '',
            '',
            $user->getCompany()
        );

        $this->em->persist($offer);
        $this->em->flush();

        return $offer;
    }
}
