<?php

declare(strict_types=1);

namespace App\Tests;

use App\Repository\UserRepository;
use App\Tests\Fixtures\Builder\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class AddUsersToCompanyTest extends WebTestCase
{
    use ResetDatabase;
    private $userBuilder;

    private $userRepository;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userBuilder = self::getContainer()->get(UserBuilder::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    /**
     * @dataProvider providerAddingUsersToCompany
     */
    public function testAddingUsersToCompany(string $userType, bool $shouldBeInCompany)
    {
        $companyOwner = $this->userBuilder->createCompanyOwner();
        $user = $this->userBuilder->{'create'.$userType}();

        $this->client->loginUser($companyOwner);

        $crawler = $this->client->request('GET', '/profile/company');
        $form = $crawler->selectButton('add_employer_form[add]')->form();
        $form['add_employer_form[email]'] = $user->getEmail();
        $this->client->submit($form);

        $user = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        if ($shouldBeInCompany) {
            $this->assertNotNull($user->getCompany());
        } else {
            $this->assertNull($user->getCompany());
        }
    }

    public function providerAddingUsersToCompany()
    {
        return [
            ['employer', true],
            ['employee', false],
        ];
    }
}
