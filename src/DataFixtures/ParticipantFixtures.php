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
            $participant = new Participant();
            $participant
                ->setPseudo($faker->userName)
            ->setPrenom($faker->firstName)
            ->setNom($faker->lastName)
            ->setEmail($faker->email)
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
