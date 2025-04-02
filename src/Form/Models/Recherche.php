<?php

namespace App\Form\Models;

use App\Entity\Lieu;
use phpDocumentor\Reflection\Types\Boolean;

class Recherche
{
    private ?string $nom = null;
    private ?\DateTimeImmutable $dateDebut = null;


    private ?string $lieu = null;
    private ?bool $organisateur = null;
    private ?bool $participant = null;
    private ?bool $nonParticipant = null;

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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): void
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