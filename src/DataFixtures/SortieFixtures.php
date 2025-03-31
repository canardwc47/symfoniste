<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;



class SortieFixtures extends Fixture implements DependentFixtureInterface
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
            $sortie->setNomSortie($faker->sentence(2));
            $sortie->setEtat($faker->randomElement($etat));
            $sortie->setDuree($faker->numberBetween(30, 240));
            $etatSortie = $sortie->getEtat()->getLibelle();
                if ($etatSortie == 'Créée' || $etatSortie == 'Ouverte') {
                    $dateHeureDebut = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 days', '+2 months'));
                } elseif ($etatSortie == 'Passée' || $etatSortie == 'Archivée') {
                    $dateHeureDebut = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 month'));
                } else {
                    $dateHeureDebut = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week'));
                }
            $sortie->setDateHeureDebut($dateHeureDebut);
            $dateString = $dateHeureDebut->format('Y-m-d H:i:s');
            $dateLimiteInscription = DateTimeImmutable::createFromMutable($faker->dateTimeBetween( $dateString, '+2 months'));
            $sortie->setDateLimiteInscription($dateLimiteInscription);

            $sortie->setNbInscriptionsMax($faker->numberBetween(5, 50));
            $sortie->setInfosSortie($faker->paragraph);
            $sortie->setOrganisateur($faker->randomElement($participants));
            $sortie->addParticipant($sortie->getOrganisateur());
            if ($sortie->getEtat()->getLibelle() != 'Créée') {
                for($y = 0; $y < rand(1,$sortie->getNbInscriptionsMax()); $y++) {
                    $sortie->addParticipant($faker->randomElement($participants));
                }
            }
            $sortie->setLieu($faker->randomElement($lieux));
            $sortie->setSite($sortie->getOrganisateur()->getSite());
            $manager->persist($sortie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LieuFixtures::class,
            EtatFixtures::class,
            ParticipantFixtures::class,
        ];
    }

}