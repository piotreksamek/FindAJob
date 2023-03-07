<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\Offer;
use Zenstruck\Foundry\ModelFactory;

class OfferFactory extends ModelFactory
{
    public function __construct()
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
            'name' => self::faker()->realText(20),
            'description' => self::faker()->realText(255),
            'price' => rand(3800, 10000),
            'dateTime' => self::faker()->dateTimeBetween('-5days', '-1 minute'),
            'slug' => self::faker()->slug(),
            'city' => self::faker()->city(),
            'company' => self::faker()->company(),
        ];
    }

    protected static function getClass(): string
    {
        return Offer::class;
    }
}
