<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Enum\Role;
use App\Tests\Fixtures\Builder\Employer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateCompanyTest extends WebTestCase
{
    private $client;

    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
    }

    public function testCreateACompanyByUserWithRoleEmployer()
    {
        $user = $this->userRepository->findUserByEmail('john.doe@example.com');

        $this->client->loginUser($user);
        $this->client->request('GET', '/profile');

        $crawler = $this->client->request('GET', '/register/company');

        $form = $crawler->selectButton('register_company_form[submit]')->form();
        $form['register_company_form[name]'] = 'Google';
        $form['register_company_form[city]'] = 'Wrocław';
        $this->client->submit($form);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
//        $this->assertTrue($user->hasRoles(Role::ROLE_OWNER_COMPANY));
    }

    public function testCreateACompanyByUserWithInvalidData()
    {
        $user = $this->userRepository->findUserByEmail('john.doe@example.com');

        $this->client->loginUser($user);
        $this->client->request('GET', '/profile');

        $crawler = $this->client->request('GET', '/register/company');

        $form = $crawler->selectButton('register_company_form[submit]')->form();
        $form['register_company_form[name]'] = '';
        $form['register_company_form[city]'] = '';
        $this->client->submit($form);

        $this->assertStringContainsString('This value should not be blank.', $this->client->getResponse()->getContent());
        $this->assertTrue($user->hasRoles(Role::ROLE_EMPLOYER));
    }

    public function testCreateACompanyByUserWithRoleEmployee()
    {
        $user = $this->userRepository->findUserByEmail('john.doe@example.com');

        $this->client->loginUser($user);
        $this->client->request('GET', '/register/company');

        $this->assertTrue($user->hasRoles(Role::ROLE_EMPLOYEE));
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateACompanyByUserWithRoleOwnerCompany()
    {
        $user = $this->userRepository->findUserByEmail('john.doe@example.com');

        $this->client->loginUser($user);
        $this->client->request('GET', '/register/company');

        $this->assertTrue($user->hasRoles(Role::ROLE_OWNER_COMPANY));
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
    }
}
