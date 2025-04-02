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

    public function afficherSorties(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->join('s.etat', 'e')
            ->where('e.libelle != :etat')
            ->setParameter('etat', 'Archivée')
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function rechercheSortie(
        Recherche $recherche,
        Security  $security)
    {

        $qB = $this->createQueryBuilder('s')
            ->leftJoin('s.lieu', 'lieu')->addSelect('lieu')
            ->leftJoin('s.participants', 'p')->addSelect('p')
            ->leftJoin('s.organisateur', 'o')->addSelect('o')
            ->leftJoin('s.etat', 'e')->addSelect('e')
            ->leftJoin('s.site', 'site')->addSelect('site');
            //->select('s')
//            ->distinct();
        $user = $security->getUser();


        //Recherche par nom de Sortie
        if ($recherche->getNom()) {
            $qB->andWhere('s.nomSortie LIKE :nom')
                ->setParameter('nom', '%' . $recherche->getNom() . '%');
        }
        //Recherche si je suis l'organisateur de la sortie

        if ($recherche->getOrganisateur()) {
            $qB->andWhere('s.organisateur = :user')
                ->setParameter('user', $user); // Utiliser l'utilisateur connecté
        }

        //Recherche si je suis inscrit a la sortie
        if ($recherche->getParticipant()) {
            $qB->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }
//
//        // Filtre pour non inscrit
        if ($recherche->getNonParticipant()) {
            $qB->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }
//
//        // Recherche par date de sortie
        if ($recherche->getDateDebut() && $recherche->getDateFin()) {
            if ($recherche->getDateFin() >= $recherche->getDateDebut()) {
                $qB->andWhere('s.dateHeureDebut BETWEEN :dateDebut AND :dateFin')
                    ->setParameter('dateDebut', $recherche->getDateDebut())
                    ->setParameter('dateFin', $recherche->getDateFin());
            }
        } elseif ($recherche->getDateDebut()) {
            $qB->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $recherche->getDateDebut());
        } elseif ($recherche->getDateFin()) {
            $qB->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $recherche->getDateFin());
        }


        if ($recherche->getSortiesPassees()) {
            $qB->andWhere('s.dateHeureDebut < :now')
                ->setParameter('now', new \DateTimeImmutable());
        }
        //Recherche par lieu de sorties
        if ($recherche->getSite()) {
        $qB->andWhere('site.id = :siteId')
            ->setParameter('siteId', $recherche->getSite()->getId());
    }

        return $qB->
        getQuery()
            ->getResult();
    }

}
