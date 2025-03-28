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

        $villes = [
            ["nom" => "Rennes", "code_postal" => "35000"],
            ["nom" => "Nantes", "code_postal" => "44000"],
            ["nom" => "Caen", "code_postal" => "14000"],
            ["nom" => "St-Herblain", "code_postal" => "44800"],
            ["nom" => "Brest", "code_postal" => "29200"],
            ["nom" => "Quimper", "code_postal" => "29000"],
            ["nom" => "St-Malo", "code_postal" => "35400"],
            ["nom" => "Le Mans", "code_postal" => "72000"],
        ];


        foreach ($villes as $data) {
            $ville = new Ville();
            $ville->setNom($data["nom"]);
            $ville->setCodePostal($data["code_postal"]);
            $manager->persist($ville);
        }


        $manager->flush();
    }
}
