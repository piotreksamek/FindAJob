<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testUserCanBeRegistered()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('register_form_save')->form();
        $form['register_form[firstName]'] = 'John';
        $form['register_form[lastName]'] = 'Snow';
        $form['register_form[email]'] = 'john.doe@example.com';
        $form['register_form[password]'] = 'password';
        $form['register_form[isEmployer]']->tick();

        $client->submit($form);

        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findBy(['email' => 'john.doe@example.com']);

        $this->assertCount(1, $user);
        $this->assertResponseRedirects('/profile');
    }

    public function testUserCanNotBeRegistered()
    {
        $this->markTestSkipped();
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('register_form_save')->form();
        $form['register_form[firstName]'] = '';
        $form['register_form[lastName]'] = '';
        $form['register_form[email]'] = 'invalid-email';
        $form['register_form[password]'] = 'password';
        $form['register_form[isEmployer]']->tick();

        $client->submit($form);

        $this->assertSelectorExists('.form-error-message');
//        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
//        $user = $userRepository->findBy(['email' => 'john.doe@example.com']);
//
//        $this->assertCount(1, $user);
//        $this->assertResponseRedirects('/');
    }
}
