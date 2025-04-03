<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Faker\Core\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PSEUDO', fields: ['pseudo'])]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit contenir au moins 2 caractères',
        maxMessage: 'Le nom ne peut pas dépasser 50 caractères' )]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le prénom doit contenir au moins 2 caractères',
        maxMessage: 'Le prénom ne peut pas dépasser 50 caractères' )]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/',
        message: 'Le numéro de téléphone n\'est pas valide')]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le pseudo est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le pseudo doit contenir au moins 2 caractères',
        maxMessage: 'Le pseudo ne peut pas dépasser 50 caractères' )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
/*    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre'
    )]*/
    private ?string $mdp = null;

    #[ORM\Column]
    #[Assert\Type(type: 'bool')]
    private ?bool $administrateur = false;

    #[ORM\Column]
    #[Assert\Type(type: 'bool')]
    private ?bool $actif = true;


    #[ORM\Column(length: 255, nullable: true)]
    private array $roles = [];

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private Collection $sorties;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur', orphanRemoval: true)]
    private Collection $sortiesOrga;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        maxSizeMessage: 'L\'image ne doit pas dépasser 1Mo',
        mimeTypesMessage: 'Veuillez télécharger une image valide (JPEG ou PNG)'
    )]
    private ?string $filename = null;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortiesOrga = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;
        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        $this->sorties->removeElement($sorty);
        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrga(): Collection
    {
        return $this->sortiesOrga;
    }

    public function addSortiesOrga(Sortie $sortiesOrga): static
    {
        if (!$this->sortiesOrga->contains($sortiesOrga)) {
            $this->sortiesOrga->add($sortiesOrga);
            $sortiesOrga->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrga(Sortie $sortiesOrga): static
    {
        if ($this->sortiesOrga->removeElement($sortiesOrga)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrga->getOrganisateur() === $this) {
                $sortiesOrga->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getRoles(): array
    {
        $roles =  $this->roles;
        //$roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles; // Convertit les rôles en une chaîne
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return (string)($this->pseudo ?? $this->email);
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }


    public function __toString(): string
    {
        return $this->nom . ' (' . $this->email . ')';
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }



}
