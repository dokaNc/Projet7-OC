<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Phone;

class PhoneFixtures extends Fixture
{
    private $models =  ['Iphone X', 'Iphone XS', 'Iphone 11', 'Iphone 11 Pro'];

    private $colors = ['black', 'white', 'gold', 'grey'];

    private $prices = [800.90, 900.90, 1000.90, 1100.90, 1200.90];

    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 20; $i++) {
            $phone = new Phone();
            $phone
                ->setBrand('Apple')
                ->setModel($this->models[rand(0,3)])
                ->setColor($this->colors[rand(0,3)])
                ->setPrice($this->prices[rand(0,4)])
                ->setDescription('A phone with ' . rand(10, 50) . ' apps');

            $manager->persist($phone);
        }

        $manager->flush();
    }
}
