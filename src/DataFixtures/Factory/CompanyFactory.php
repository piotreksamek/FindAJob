<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Company;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;

class CompanyFactory extends ModelFactory
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
    }

    protected function initialize()
    {
       return $this;
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'name' => self::faker()->company(),
            // todo wrzucić usera
        ];
    }

    protected static function getClass(): string
    {
        return Company::class;
    }
}
