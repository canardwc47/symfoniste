<?php

namespace App\Form\Models;

use App\Entity\Lieu;
use App\Entity\Site;
use Symfony\Component\Validator\Constraints as Assert;

class Recherche
{
    #[Assert\Length(max: 50, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $nom = null;

    #[Assert\Type("\DateTimeImmutable", message: "La date de début doit être une date valide")]
    private ?\DateTimeImmutable $dateDebut = null;

    #[Assert\Type("\DateTimeImmutable", message: "La date de fin doit être une date valide")]
    #[Assert\Expression(
        "this.getDateDebut() === null || this.getDateFin() === null || this.getDateDebut() <= this.getDateFin()",
        message: "La date de fin doit être postérieure à la date de début"
    )]
    private ?\DateTimeImmutable $dateFin = null;


    private ?Lieu $lieu = null;
    private ?Site $site = null;
    private ?bool $organisateur = false;
    private ?bool $participant = false;
    private ?bool $nonParticipant = false;
    private ?bool $sortiesPassees = false;



    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): void
    {
        $this->site = $site;
    }



    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }

    public function setSortiesPassees(?bool $sortiesPassees): void
    {
        $this->sortiesPassees = $sortiesPassees;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getNonParticipant(): ?bool
    {
        return $this->nonParticipant;
    }

    public function setNonParticipant(?bool $nonParticipant): void
    {
        $this->nonParticipant = $nonParticipant;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeImmutable $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): void
    {
        $this->lieu = $lieu;
    }

    public function getOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    public function getParticipant(): ?bool
    {
        return $this->participant;
    }

    public function setParticipant(?bool $participant): void
    {
        $this->participant = $participant;
    }
}