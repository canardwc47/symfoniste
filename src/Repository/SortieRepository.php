<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function findByOrganisateur($organisateur): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = :organisateur')
            ->setParameter('organisateur', $organisateur)
            ->orderBy('s.dateHeureDebut', 'DESC')// Trier par date décroissante par exemple
            ->getQuery()
            ->getResult();
    }

    public function findByInscrit($participant): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.participants', 'p') // Jointure avec la table des participants
            ->andWhere('p = :participant')
            ->setParameter('participant', $participant)
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByNonInscrit($participant): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.participants', 'p') // Jointure avec la table des participants
            ->andWhere('p IS NULL OR p != :participant')
            ->setParameter('participant', $participant)
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

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
