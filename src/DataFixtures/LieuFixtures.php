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

        $prefix = ["Au", "The", "El", "My", "Du"];
        $adjectives = ["Fou", "Electro", "VIP", "Nocturne", "Secret", "Cosmique", "Rouge", "Noir", "Bleu", "Doré", "Fou", "Secret", "Sexy", "Famoso", "Caliente"];
        $nouns = ["Bouffe", "Lounge", "Palace", "Spot", "Temple", "Fusion", "Vortex", "Dynamite", "Cocktail", "Bistrot", "Cave", "Perroquet", "Piano", "Brasserie", "Canaille", "Loutre"];



        for ($i = 0; $i < 20; $i++) {
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
