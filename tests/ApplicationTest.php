<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\ApplicationRepository;
use App\Tests\Fixtures\Builder\ApplicationBuilder;
use App\Tests\Fixtures\Builder\OfferBuilder;
use App\Tests\Fixtures\Builder\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApplicationTest extends WebTestCase
{
    use ResetDatabase;

    private $userBuilder;

    private $applicationRepository;

    private $applicationBuilder;

    private $offerBuilder;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userBuilder = static::getContainer()->get(UserBuilder::class);
        $this->offerBuilder = static::getContainer()->get(OfferBuilder::class);
        $this->applicationRepository = static::getContainer()->get(ApplicationRepository::class);
        $this->applicationBuilder = static::getContainer()->get(ApplicationBuilder::class);
    }

    public function testEmployeeCanAddApplicationToOffer()
    {
        $user = $this->userBuilder->createEmployee();
        $offer = $this->offerBuilder->createOffer();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', 'offers/application/' . $offer->getSlug());
        $form = $crawler->selectButton('application_form[apply]')->form();
        $form['application_form[description]'] = 'LOREM IPSUM LOREM IPSUM LOREM IPSUM';
        $this->client->submit($form);

        $application = $this->applicationRepository->findOneBy(['owner' => $user->getId()]);
        $this->assertNotNull($application);
    }

    public function testEmployerCanAddApplicationToOffer()
    {
        $user = $this->userBuilder->createEmployer();
        $offer = $this->offerBuilder->createOffer();
        $this->client->loginUser($user);

        $this->client->request('GET', 'offers/application/' . $offer->getSlug());

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAnonymousUserCanAddApplicationToOffer()
    {
        $offer = $this->offerBuilder->createOffer();

        $this->client->request('GET', 'offers/application/' . $offer->getSlug());

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
    }

    public function testOwnerApplicationCanSeeHisPost()
    {
        $application = $this->applicationBuilder->createApplication();
        $user = $application->getOwner();

        $this->client->loginUser($user);

        $this->client->request('GET', '/application/'. $user->getUsername() . '/' . $application->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testOwnerCompanyCanSeeSomeoneApplication()
    {
        $application = $this->applicationBuilder->createApplication();
        $user = $application->getOwner();
        $companyOwner = $application->getOffer()->getOwner()->getOwner();

        $this->client->loginUser($companyOwner);

        $this->client->request('GET', '/application/'. $user->getUsername() . '/' . $application->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testUserCanSeeSomeoneApplication()
    {
        $application = $this->applicationBuilder->createApplication();
        $user = $application->getOwner();

        $this->client->request('GET', '/application/'. $user->getUsername() . '/' . $application->getId());

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
    }
}
