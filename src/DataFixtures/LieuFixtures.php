<?php

namespace App\DataFixtures;
use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $ville = $manager->getRepository(Ville::class)->findAll();

        $prefix = ["Au", "The", "El", "My", "Du", "", "", "Das", "One", "Ein"];
        $adjectives = ["Nocturne", "Gemütlich","Rustikal", "Secret", "Abgefahren","Cosmique", "Rouge", "Noir", "Bleu", "Doré", "Fou", "Secret", "Sexy", "Famoso", "Caliente", "Dancing", "Special", "Chill", "Crazy"];
        $nouns = ["Lounge", "Palace", "Spot", "Temple", "Fusion", "Vortex", "Dynamite", "Cocktail", "Bistrot", "Cave", "Piano", "Brasserie", "Canaille", "Loutre", "Bar", "Mondschein", "Versteck", "Gasthaus", "Rooftop", "Kantine"]; ];



        for ($i = 0; $i < 30; $i++) {
            $lieu = new Lieu();
            $fakename = $prefix[array_rand($prefix)] . " " . $adjectives[array_rand($adjectives)] . " " . $nouns[array_rand($nouns)];
            $lieu->setNomLieu($fakename);
            $lieu->setRue($faker->streetName);
            $lieu->setLatitude($faker->randomFloat(6, 46.5, 50.0));
            $lieu->setLongitude($faker->randomFloat(6, -5.0, 0.0));
            $lieu->setVille($faker->randomElement($ville));
            $manager->persist($lieu);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            VilleFixtures::class,
        ];
    }

}
