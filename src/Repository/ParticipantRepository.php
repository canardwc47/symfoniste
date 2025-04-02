<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class ParticipantRepository extends ServiceEntityRepository implements UserProviderInterface, PasswordUpgraderInterface, UserLoaderInterface
{

    // Test constructeur pour vérifier si le formulaire marche
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @throws UserNotFoundException if the user is not found
     */
    public function loadUserByIdentifier($identifier): UserInterface
    {
        return $this->createQueryBuilder('p')
            ->where('p.email = :identifier OR p.pseudo = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @deprecated since Symfony 5.3, loadUserByIdentifier() is used instead
     */


    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Participant) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }
        // Recherche l'utilisateur dans la base de données en utilisant son identifiant (ID ou email)
        $user = $this->find($user->getId());

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with id "%d" not found.', $user->getId()));
        }

        return $user;
    }


    public function findOneByUser($user): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom = :participant')  // Assurez-vous que 'user' est bien le champ dans l'entité Participant
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();  // Renvoie un seul résultat ou null
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass(string $class): bool
    {
        return Participant::class === $class || is_subclass_of($class, Participant::class);
    }

    /**
     * Upgrades the hashed password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // TODO: when hashed passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newHashedPassword);
    }

    //Fonction pour retrouver plusieurs détails d'un participant
    public function detailSortiesParticipant(int $participantId): array
    {
        return $this->createQueryBuilder('p')
            ->select(
                'p.id AS participant_id',
                's.dateHeureDebut AS sortie_date',
                's.nomSortie AS sortie_nom',
                'o.pseudo AS sortie_organisateur',
                //'o.site AS sortie_site',
                'l.nomLieu AS lieu_nom',
                'v.nom AS ville_nom',
                'e.libelle AS etat_libelle'
            )
            ->join('p.sorties', 's')
            ->join('s.organisateur', 'o')
            ->join('s.etat', 'e')
            ->join('s.lieu', 'l')
            ->join('l.ville', 'v')
            ->where('p.id = :participantId')
            ->setParameter('participantId', $participantId)
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

}