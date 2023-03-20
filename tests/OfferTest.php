<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use App\Tests\Fixtures\Builder\OfferBuilder;
use App\Tests\Fixtures\Builder\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class OfferTest extends WebTestCase
{
    use ResetDatabase;

    private $userBuilder;

    private $offerBuilder;

    private $offerRepository;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userBuilder = self::getContainer()->get(UserBuilder::class);
        $this->offerBuilder = self::getContainer()->get(OfferBuilder::class);
        $this->offerRepository = self::getContainer()->get(OfferRepository::class);
    }

    public function testAddOfferByCompanyOwner()
    {
        $companyOwner = $this->userBuilder->createCompanyOwner();

        $this->client->loginUser($companyOwner);

        $crawler = $this->client->request('GET', '/profile/company/new/offer');
        $form = $crawler->selectButton('create_offer_form[submit]')->form();
        $form['create_offer_form[name]'] = 'PHP DEVELOPER';
        $form['create_offer_form[city]'] = '';
        $form['create_offer_form[price]'] = '';
        $form['create_offer_form[description]'] = 'LOREM IPSUM LOREM IPSUM LOREM IPSUM LOREM IPSUM';
        $this->client->submit($form);

        $offer = $this->offerRepository->findOneBy(['name' => 'PHP DEVELOPER']);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->client->request('GET', '/offers/' . $offer->getSlug());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAddOfferByEmployee()
    {
        $employee = $this->userBuilder->createEmployee();

        $this->client->loginUser($employee);

        $this->client->request('GET', '/profile/company/new/offer');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /** @dataProvider providerEditOffer */
    public function testEditOfferByCompanyOwner(string $userType, bool $canEdit)
    {
        /** @var Offer $offer */
        $offer = $this->offerBuilder->createOffer();
        $user = $offer->getOwner()->getOwner();

        if ($userType === 'employer') {
            $this->client->loginUser($user);
        }

        $crawler = $this->client->request('GET', '/offers/edit/' . $offer->getSlug());

        if (!$canEdit) {
            $this->assertTrue($this->client->getResponse()->isRedirect());
            return;
        }

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('edit_offer_form[submit]')->form();
        $form['edit_offer_form[city]'] = 'Warszawa';
        $form['edit_offer_form[price]'] = '2000';
        $form['edit_offer_form[description]'] = 'ASDASDASD';
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $offer = $this->offerRepository->findOneBy(['city' => 'Warszawa']);

        $this->assertNotNull($offer);
    }

    public function providerEditOffer()
    {
        return [
            ['employer', true],
            ['employee', false],
        ];
    }

    public function testDeleteOfferByOwner()
    {
        $offer = $this->offerBuilder->createOffer();

        $user = $offer->getOwner()->getOwner();

        $this->client->loginUser($user);

        $this->client->request('GET', '/offers/delete/' . $offer->getSlug());

        $offer = $this->offerRepository->findOneBy(['name' => $offer->getName()]);
        $this->assertNull($offer);
    }

    public function testDeleteOfferByOtherUsers()
    {
        $offer = $this->offerBuilder->createOffer();

        $this->client->request('GET', '/offers/delete/' . $offer->getSlug());

        $offer = $this->offerRepository->findOneBy(['name' => $offer->getName()]);

        $this->assertNotNull($offer);
    }
}
