<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Fixtures\Builder\UserBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginUserTest extends WebTestCase
{
    use ResetDatabase;

    private $client;

    private $userBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userBuilder = self::getContainer()->get(UserBuilder::class);
    }

    public function testUserCanBeLogin()
    {
        $user = $this->userBuilder->createEmployee();

        $this->client->loginUser($user);
        $this->client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        $this->assertNull($this->client->getRequest()->getSession()->get('_security.last_error'));
    }

    public function testUserCannotBeLogin()
    {
        $this->userBuilder->createEmployee();

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['email'] = 'employer@gmail.com';
        $form['_password'] = 'invalid-password';
        $form['_remember_me'] = 'on';
        $this->client->submit($form);

        $this->assertNotNull($this->client->getRequest()->getSession()->get('_security.last_error'));
    }
}
