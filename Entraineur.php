<?php

    require_once '../db/DAOEntraineur.php';
    class Entraineur {
        private int $idEntraineur;
        private string $nom;
        private string $prenom;
        private string $email;

        private string $equipe;

        public function  __construct(int $idEntraineur, string $nom, string $prenom, string $email, string $equipe) {
            $this -> idEntraineur = $idEntraineur;
            $this -> nom = $nom;
            $this -> prenom = $prenom;
            $this -> email = $email;
            $this -> equipe = $equipe;
        }

        public function getIdEntraineur(): int {
            return $this -> idEntraineur;
        }

        public function getNom(): string {
            return $this -> nom;
        }

        public function getPrenom(): string {
            return $this -> prenom;
        }

        public function getEmail(): string {
            return $this -> email;
        }

        public function getEquipe(): string {
            return $this -> equipe;
        }
        // partie DAO
        public function inscriptionEntraineur(string $motdepasse): void {
            DAOEntraineur::create($this,$motdepasse);
        }

        public function modifierMotDePasse(string $nouveauMotDePasse): void {
            DAOEntraineur::update($this,$nouveauMotDePasse);
        }

        /**
         * @throws Exception
         */
        public static function getEntraineur(string $email, string $motdepasse): ?Entraineur{
            if(DAOEntraineur::existEntraineurWithEmail($email)) {
                if(DAOEntraineur::compareMotDePasse($motdepasse, $email)){
                    return DAOEntraineur::getEntraineur($email);
                }
                echo "test : exist email but wrong password";
                die();
            }
            return null;
        }
    }
