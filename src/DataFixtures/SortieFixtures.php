<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;



class SortieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Retrieve related entities (assuming you have them)
        $sites = $manager->getRepository(Site::class)->findAll();
        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $etat = $manager->getRepository(Etat::class)->findAll();
        $participants = $manager->getRepository(Participant::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $sortie = new Sortie();
            $sortie->setNomSortie($faker->sentence(3));
            $sortie->setDateHeureDebut(
                $faker->dateTimeBetween('+1 days', '+2 months')
            );
            $sortie->setDuree($faker->numberBetween(30, 240));
            $sortie->setDateLimiteInscription(
                $faker->dateTimeBetween('now', '+1 month')
            );
            $sortie->setNbInscriptionsMax($faker->numberBetween(5, 50));
            $sortie->setInfosSortie($faker->paragraph);
            $sortie->setOrganisateur($faker->randomElement($participants));
            $sortie->setLieu($faker->randomElement($lieux));
            $sortie->setEtat($faker->randomElement($etat));
            $sortie->setSite($faker->randomElement($sites));

            $manager->persist($sortie);
        }

        $manager->flush();
    }
}