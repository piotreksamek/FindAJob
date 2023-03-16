<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginUserTest extends WebTestCase
{
    public function testUserCanBeLogin()
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);

        $user = $userRepository->findUserByEmail('john.doe@example.com');

        $client->loginUser($user);
        $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        $this->assertNull($client->getRequest()->getSession()->get('_security.last_error'));
    }

    public function testUserCannotBeLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['email'] = 'john.doe@example.com';
        $form['_password'] = 'password';
        $form['_remember_me'] = 'on';
        $client->submit($form);

        $this->assertNotNull($client->getRequest()->getSession()->get('_security.last_error'));
    }
}
