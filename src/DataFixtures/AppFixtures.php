<?php

namespace App\DataFixtures;

use App\Factory\CompanyFactory;
use App\Factory\OfferFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'test@gmail.com',
            'password' => '123123',
        ]);

        UserFactory::createMany(10);

        OfferFactory::createMany(10);

        CompanyFactory::createMany(10);

//        SubmissionFactory::createMany(10);

        $manager->flush();
    }
}
