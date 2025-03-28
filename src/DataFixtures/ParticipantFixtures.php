<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $site = $manager->getRepository(Site::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $prenom = $faker->firstName;
            $nom = $faker->lastName;

            $domains = [
                'gmail.com',
                'yahoo.com',
                'wanadoo.fr',
                'outlook.com',
                'hotmail.com',
                'msn.com',
                'live.com',

            ];
            $email = strtolower(str_replace(' ', '', $prenom) . '.' . str_replace(' ', '', $nom) . '@' . $domains[array_rand($domains)]);
            $pseudo = strtolower($prenom . rand(1, 999));

            $participant = new Participant();
            $participant
                ->setPseudo($pseudo)
                ->setPrenom($prenom)
                ->setNom($nom)
                ->setEmail($email)
                ->setTelephone($faker->phoneNumber)
                ->setSite($faker->randomElement($site))
                ->setActif( 1)
                ->setAdministrateur(0)
                ->setMdp(
                    $this->userPasswordHasher->hashPassword($participant, "123456")
                )->setRoles(['ROLE_USER']);

            $manager->persist($participant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SiteFixtures::class,
        ];
    }


}
