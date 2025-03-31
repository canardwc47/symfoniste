<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Curl\User;

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

//    public function findByRechercheSortie(?string $filtre, ?Participant $participant = null): array
//    {
//        $qb = $this->createQueryBuilder('s');
//
//        // Filtre pour les sorties passées
//        if ($filtre === 'passees') {
//            $qb->where('s.dateHeureDebut < :now')
//                ->setParameter('now', new \DateTime()); // Correction de la syntaxe
//        }
//
//        // Filtre pour les sorties organisées par le participant
//        elseif ($filtre === 'organisateur' && $participant) {
//            $qb->andWhere('s.organisateur = :participant')
//                ->setParameter('participant', $participant); // Correction de la syntaxe
//        }
//
//        // Filtre pour les sorties auxquelles le participant est inscrit
//        elseif ($filtre === 'inscrit' && $participant) {
//            $qb->join('s.participants', 'p')
//                ->andWhere('p = :participant')
//                ->setParameter('participant', $participant); // Correction de la syntaxe
//        }
//
//        // Filtre pour les sorties auxquelles le participant n'est pas inscrit
//        elseif ($filtre === 'noninscrit' && $participant) {
//            $qb->join('s.participants', 'p')
//                ->andWhere('p IS NULL OR p != :participant')
//                ->setParameter('participant', $participant); // Correction de la syntaxe
//        }
//        return $qb->getQuery()->getResult();
//    }



//    public function findByOrganisateur($organisateur): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.organisateur = :organisateur')
//            ->setParameter('organisateur', $organisateur)
//            ->orderBy('s.dateHeureDebut', 'DESC')// Trier par date décroissante par exemple
//            ->getQuery()
//            ->getResult();
//    }
//
//    public function findByInscrit($participant): array
//    {
//        return $this->createQueryBuilder('s')
//            ->join('s.participants', 'p') // Jointure avec la table des participants
//            ->andWhere('p = :participant')
//            ->setParameter('participant', $participant)
//            ->orderBy('s.dateHeureDebut', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }
//
//    public function findByNonInscrit($participant): array
//    {
//        return $this->createQueryBuilder('s')
//            ->join('s.participants', 'p') // Jointure avec la table des participants
//            ->andWhere('p IS NULL OR p != :participant')
//            ->setParameter('participant', $participant)
//            ->orderBy('s.dateHeureDebut', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }
//
//
//    public function findBySortiesPasses($sortiesPasses): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.dateHeureDebut < :sortiesPasses')
//            ->setParameter('sortiesPasses', $sortiesPasses)
//            ->getQuery()
//            ->getResult();
//    }
    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
