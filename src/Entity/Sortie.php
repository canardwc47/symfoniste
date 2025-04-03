<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom de la sortie est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom de la sortie doit contenir au moins 2 caractères',
        maxMessage: 'Le nom de la sortie ne peut pas dépasser 50 caractères' )]
    private ?string $nomSortie = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de la sortie est obligatoire')]
    #[Assert\GreaterThan(
        value: 'today',
        message: 'La date de la sortie doit être dans le futur'
    )]
    private ?\DateTimeImmutable $dateHeureDebut = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: 'La durée est obligatoire')]
    #[Assert\Positive(message: 'La durée doit être un nombre positif')]
    private ?int $duree = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date limite d\'inscription est obligatoire')]
    #[Assert\LessThan(
        propertyPath: 'dateHeureDebut',
        message: 'La date limite d\'inscription doit être avant la date de début de la sortie'
    )]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'La date limite d\'inscription doit être aujourd\'hui ou dans le futur'
    )]
    private ?\DateTimeImmutable $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le nombre maximum d\'inscriptions est obligatoire')]
    #[Assert\Positive(message: 'Le nombre d\'inscriptions doit être supérieur à 0')]
    #[Assert\LessThan(value: 1000, message: 'Le nombre maximum d\'inscriptions ne peut pas dépasser 999')]
    private ?int $nbInscriptionsMax = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description de la sortie est obligatoire')]
    #[Assert\Length(
        min: 10,
        max: 250,
        minMessage: 'La description de la sortie doit contenir au moins 10 caractères',
        maxMessage: 'La description de la sortie ne peut pas dépasser 50 caractères' )]
    private ?string $infosSortie = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'état de la sortie doit être défini')]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le site de la sortie doit être défini')]
    private ?Site $site = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le lieu de la sortie doit être défini')]
    private ?Lieu $lieu = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'sorties')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrga')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'organisateur de la sortie doit être défini')]
    private ?Participant $organisateur = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNomSortie(): ?string
    {
        return $this->nomSortie;
    }

    public function setNomSortie(?string $nomSortie): static
    {
        $this->nomSortie = $nomSortie;

        return $this;
    }



    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }




    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(?int $nbInscriptionsMax): static
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }


    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): Sortie
    {
        $this->site = $site;
        return $this;
    }
    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;


        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeImmutable
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTimeImmutable $dateHeureDebut): Sortie
    {
        $this->dateHeureDebut = $dateHeureDebut;
        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeImmutable
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTimeImmutable $dateLimiteInscription): Sortie
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addSorty($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeSorty($this);
        }

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getFormattedDuree(): String
    {
        $minutes= $this->duree;
        $heures = intdiv($minutes, 60);
        $minutesRestantes = $minutes % 60;

        $dureeFormate ='';
        if ($heures > 0) {
            $dureeFormate.= $heures . ' heure' . ($heures > 1 ? 's' : '') . ' ';
        }
        if ($minutesRestantes > 0 ){
            $dureeFormate .= $minutesRestantes . ' minute' . ($minutesRestantes > 1 ? 's' : '');
        }
        return $dureeFormate;
    }
}
