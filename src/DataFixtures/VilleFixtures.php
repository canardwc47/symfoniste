<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VilleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');


        for ($i = 0; $i < 20; $i++) {
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal(mt_rand(10000, 99999));
            $manager->persist($ville);
        }


        $manager->flush();
    }
}
