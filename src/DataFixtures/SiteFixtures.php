<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sites = ["St-Herblain", "Lannion", "Chartres-de-Bretagne", "Vannes", "Caen"];

        foreach ($sites as $nomSite) {
            $site = new Site();
            $site->setNomSite($nomSite);
            $manager->persist($site);
        }

        $manager->flush();
    }
}
