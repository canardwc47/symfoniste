<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\Models\Recherche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Curl\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */

    public function rechercheSortie(
        Recherche $recherche,
        Security  $security)
    {


        $qB = $this->createQueryBuilder('s');
        $user = $security->getUser();


        $nomDeSortie = $recherche->getNom();
        $dateDeSortie = $recherche->getDateDebut();
        $now = new \DateTimeImmutable();
        $organisateur = $recherche->getOrganisateur();
        $inscrit = $recherche->getParticipant();
        $nonInscrit = $recherche->getNonParticipant();
        $sortiesPassees = $recherche->getDateDebut();
        $lieu = $recherche->getLieu();



        //Recherche par nom de Sortie
        if ($nomDeSortie) {
            $qB->andWhere('s.nomSortie LIKE :nom')
                ->setParameter('nom', '%' . $nomDeSortie . '%');
        }
        //Recherche si je suis l'organisateur de la sortie
        if (true) {
            $qB->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user); // Utiliser l'utilisateur connecté
        }

        //Recherche si je suis inscrit a la sortie
        if (true) {
            $qB->andWhere(':participant MEMBER OF s.participants')
                ->setParameter('participant', $user);
        }
//
//        // Filtre pour non inscrit
//        if (true) {
//            $qB->andWhere(':user NOT MEMBER OF s.participants')
//                ->setParameter('user', $user);
//        }
//
//        // Recherche par date de sortie
//        if ($dateDeSortie) {
//            $dateDeSortieString = $dateDeSortie->format('Y-m-d');
//            $qB->andWhere('DATE(s.dateHeureDebut) = :dateDeSortie')
//                ->setParameter('dateDeSortie', $dateDeSortieString);
//        }
//
//        // Recherche des sorties passées
//        if ($sortiesPassees) {
//            $now = new \DateTimeImmutable();
//            $qB->andWhere('s.dateHeureDebut < :now')
//                ->setParameter('now', $now);
//        }

        //Recherche par lieu de sorties
        if ($lieu) {
            $qB->andWhere('s.lieu LIKE :lieu')
                ->setParameter('lieu', $lieu);
        }


        return $qB->addOrderBy('s.dateHeureDebut', 'DESC')->getQuery()->getResult();
    }

}
