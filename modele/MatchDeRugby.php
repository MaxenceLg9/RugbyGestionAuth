<?php

require '../modele/Resultat.php';
require '../modele/Lieu.php';
include '../db/DAOMatchDeRugby.php';

class MatchDeRugby {

    private ?int $idMatch;
    private DateTime $dateHeure;
    private string $adversaire;
    private Lieu $lieu;
    private ?Resultat $resultat = null;

    private bool $valider;

    function __construct(int $idMatch, DateTime $dateHeure, string $adversaire, Lieu $lieu, bool $archive) {
        $this -> idMatch = $idMatch;
        $this -> dateHeure = $dateHeure;
        $this -> adversaire = $adversaire;
        $this -> lieu = $lieu;
        $this->valider = $archive;
    }

    public static function getFromId(int $idMatch): ?MatchDeRugby
    {
        return DAOMatchDeRugby::readById($idMatch);
    }

    public function getIdMatch(): int {
        return $this -> idMatch;
    }

    /**
     * @param bool $valider
     */
    public function setValider(bool $valider): void
    {
        $this->valider = $valider;
    }

    /**
     * @return bool
     */
    public function isValider(): bool
    {
        return $this->valider;
    }


    /**
     * @param DateTime $dateHeure
     */
    public function setDateHeure(DateTime $dateHeure): void
    {
        $this->dateHeure = $dateHeure;
    }

    public function getDateHeure(): DateTime {
        return $this -> dateHeure;
    }

    public function setAdversaire(string $adversaire): void
    {
        $this->adversaire = $adversaire;
    }

    public function getAdversaire(): string {
        return $this -> adversaire;
    }

    /**
     * @param Lieu $lieu
     */
    public function setLieu(Lieu $lieu): void
    {
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

    // partie DAO : utilisation des méthodes de la classe DAOMatchDeRugby
    public function saveMatchDeRugby(): void {
        $daoMatchDeRugby = new DAOMatchDeRugby();
        if ($this->getIdMatch() === -1) {
            $daoMatchDeRugby -> create($this);
            return;
        }
        $daoMatchDeRugby -> update($this);
    }

    public static function findAll(): array {
        return DAOMatchDeRugby::read();
    }

    //TODO : ?????
    public function getMatchDeRugbyByDateHeure(): MatchDeRugby {
        $daoMatchDeRugby = new DAOMatchDeRugby();
        return $daoMatchDeRugby -> readByDateHeure($this -> dateHeure);
    }

    public static function getMatchDeRugbyWithResultat(): array {
        $daoMatchDeRugby = new DAOMatchDeRugby();
        return $daoMatchDeRugby -> readMatchWithResultat();
    }

    public function deleteMatchDeRugby(): void {
        $daoMatchDeRugby = new DAOMatchDeRugby();
        $daoMatchDeRugby -> delete($this);
    }

    public function validerMatch(): void
    {
        $daoMatchDeRugby = new DAOMatchDeRugby();
        $daoMatchDeRugby->validerMatch($this);
    }
}
