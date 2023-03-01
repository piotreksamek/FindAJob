<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Offer;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;

class UserFactory extends ModelFactory
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
    }

    protected function initialize()
    {
        return $this
            ->afterInstantiate(function(User $user) {
                if($user->getPlainPassword()){
                    $user->setPassword($this->hasher->hashPassword($user, $user->getPlainPassword()));
                }
            })
            ;
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'first_name' => self::faker()->firstName(),
            'last_name' => self::faker()->lastName(),
            'plainPassword' => '123123',
            ];
    }

    protected static function getClass(): string
    {
        return User::class;
    }

}