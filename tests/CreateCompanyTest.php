<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Company;
use App\Enum\Role;
use App\Tests\Fixtures\Builder\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class CreateCompanyTest extends WebTestCase
{
    use ResetDatabase;

    private $client;

    private $userBuilder;

    private $companyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userBuilder = static::getContainer()->get(UserBuilder::class);
        $this->companyRepository = static::getContainer()->get('doctrine')->getRepository(Company::class);
    }

    public function testCreateACompanyByUserWithRoleEmployer()
    {
        $employer = $this->userBuilder->createEmployer();

        $this->client->loginUser($employer);

        $crawler = $this->client->request('GET', '/register/company');

        $form = $crawler->selectButton('register_company_form[submit]')->form();
        $form['register_company_form[name]'] = 'Google';
        $form['register_company_form[city]'] = 'Wrocław';
        $this->client->submit($form);

        $users = $this->companyRepository->findOneBy(['name' => 'Google'])->getUsers()->toArray();

        foreach ($users as $user) {
            $email = $user->getEmail();
        }

        $this->assertSame('employer@gmail.com', $email);
        $this->assertSame('Google', $this->companyRepository->findOneBy(['name' => 'Google'])->getName());
    }

    public function testCreateACompanyByUserWithInvalidData()
    {
        $employer = $this->userBuilder->createEmployer();

        $this->client->loginUser($employer);

        $crawler = $this->client->request('GET', '/register/company');

        $form = $crawler->selectButton('register_company_form[submit]')->form();
        $form['register_company_form[name]'] = '';
        $form['register_company_form[city]'] = '';
        $this->client->submit($form);

        $this->assertStringContainsString('This value should not be blank.', $this->client->getResponse()->getContent());
    }

    public function testCreateACompanyByUserWithRoleEmployee()
    {
        $employee = $this->userBuilder->createEmployee();

        $this->client->loginUser($employee);
        $this->client->request('GET', '/register/company');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($employee->hasRoles(Role::ROLE_EMPLOYEE));
    }

    public function testCreateACompanyByUserWithRoleOwnerCompany()
    {
        $companyOwner = $this->userBuilder->createCompanyOwner();

        $this->client->loginUser($companyOwner);
        $this->client->request('GET', '/register/company');

        $this->assertTrue($companyOwner->hasRoles(Role::ROLE_OWNER_COMPANY));
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
    }
}
