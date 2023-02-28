<?php

namespace App\DataFixtures;

use App\Factory\OfferFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        OfferFactory::createMany(10);

        $manager->flush();
    }
}
