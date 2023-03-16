<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginUserTest extends WebTestCase
{
    private $client;
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

    }

    public function testUserCanBeLogin()
    {

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findUserByEmail('john.doe@example.com');

        $this->client->loginUser($user);
        $this->client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        $this->assertNull($this->client->getRequest()->getSession()->get('_security.last_error'));
    }

    public function testUserCannotBeLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['email'] = 'john.doe@example.com';
        $form['_password'] = 'invalid-password';
        $form['_remember_me'] = 'on';
        $this->client->submit($form);

        $this->assertNotNull($this->client->getRequest()->getSession()->get('_security.last_error'));
    }
}
