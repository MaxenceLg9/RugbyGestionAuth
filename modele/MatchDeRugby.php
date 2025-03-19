<?php

require '../modele/Resultat.php';
require '../modele/Lieu.php';

class MatchDeRugby {

    private ?int $idMatch;
    private DateTime $dateHeure;
    private string $adversaire;
    private Lieu $lieu;
    private ?Resultat $resultat = null;

    private bool $valider;

    function __construct(?int $idMatch, DateTime $dateHeure, string $adversaire, Lieu $lieu, bool $archive) {
        $this -> idMatch = $idMatch;
        $this -> dateHeure = $dateHeure;
        $this -> adversaire = $adversaire;
        $this -> lieu = $lieu;
        $this -> valider = $archive;
    }

    public function getIdMatch(): int {
        return $this -> idMatch;
    }

    public function setValider(bool $valider): void {
        $this->valider = $valider;
    }

    public function isValider(): bool {
        return $this->valider;
    }

    public function setDateHeure(DateTime $dateHeure): void {
        $this->dateHeure = $dateHeure;
    }

    public function getDateHeure(): DateTime {
        return $this -> dateHeure;
    }

    public function setAdversaire(string $adversaire): void {
        $this->adversaire = $adversaire;
    }

    public function getAdversaire(): string {
        return $this -> adversaire;
    }

    public function setLieu(Lieu $lieu): void {
        $this->lieu = $lieu;
    }

    public function getLieu(): Lieu {
        return $this -> lieu;
    }

    public function getResultat(): ?Resultat {
        return is_null($this -> resultat) ? null : $this -> resultat;
    }

    public function setResultat(Resultat $resultat): void {
        $this -> resultat = $resultat;
    }

}
