<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Model\ListedExceptions;

/**
 * Class métier Médecin
 */
class Medecin implements Entity {

    private ?int $idMedecin;
    private string $civilite;
    private string $nom;
    private string $prenom;

    /**
     * Constructeur de la classe Médecin
     * @param int|null $idMedecin L'identifiant du médecin (null si nouveau médecin)
     * @param string $civilite La civilité du médecin (M ou Mme)
     * @param string $nom Le nom du médecin (20 caractères maximum)
     * @param string $prenom Le prénom du médecin (20 caractères maximum)
     * @throws ListedExceptions Si un des paramètres est invalide
     */
    public function __construct(?int $idMedecin, string $civilite, string $nom, string $prenom) {
        $exceptions = new ListedExceptions();

        if(empty($civilite) || empty($nom) || empty($prenom)) {
            $exceptions->addException(new \InvalidArgumentException('Tous les champs doivent être remplis'));
        }
        if($idMedecin != null && $idMedecin <= 0) {
            $exceptions->addException(new \InvalidArgumentException('idMedecin doit être supérieur à 0'));
        }
        if($civilite != 'M' && $civilite != 'Mme') {
            $exceptions->addException(new \InvalidArgumentException('La civilité doit être égale à "M" ou "Mme"'));
        }
        if(strlen($nom) > 20) {
            $exceptions->addException(new \InvalidArgumentException('Le nom doit être inférieur à 20 caractères'));
        }
        if(strlen($prenom) > 20) {
            $exceptions->addException(new \InvalidArgumentException('Le prénom doit être inférieur à 20 caractères'));
        }

        if (!$exceptions->isEmpty()) {
            throw $exceptions;
        }
        
        $this->idMedecin = $idMedecin;
        $this->civilite = $civilite;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    /**
     * Retourne l'identifiant du médecin
     * @return ?int L'identifiant du médecin (null si nouveau médecin)
     */
    public function getIdMedecin(): ?int {
        return $this->idMedecin;
    }

    /**
     * Modifie l'identifiant du médecin
     * @param int $idMedecin L'identifiant du médecin
     * @throws \InvalidArgumentException Si l'identifiant est null ou inférieur à 0
     */
    public function setIdMedecin(int $idMedecin): void {
        if($idMedecin == null) {
            throw new \InvalidArgumentException('idMedecin ne peut être null');
        }
        if($idMedecin <= 0) {
            throw new \InvalidArgumentException('idMedecin doit être supérieur à 0');
        }

        $this->idMedecin = $idMedecin;
    }

    /**
     * Retourne la civilité du médecin
     * @return string La civilité du médecin (M ou Mme)
     */
    public function getCivilite(): string {
        return $this->civilite;
    }

    /**
     * Modifie la civilité du médecin
     * @param string $civilite La civilité du médecin (M ou Mme)
     * @throws \InvalidArgumentException Si la civilité est null ou différente de M ou Mme
     */
    public function setCivilite(string $civilite): void {
        if($civilite == null) {
            throw new \InvalidArgumentException('civilite ne peut être null');
        }
        if($civilite != 'M' && $civilite != 'Mme') {
            throw new \InvalidArgumentException('civilite doit être égal à "M" ou "Mme"');
        }

        $this->civilite = $civilite;
    }

    /**
     * Retourne le nom du médecin
     * @return string Le nom du médecin (20 caractères maximum)
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Modifie le nom du médecin
     * @param string $nom Le nom du médecin (20 caractères maximum)
     * @throws \InvalidArgumentException Si le nom est null ou supérieur à 20 caractères
     */
    public function setNom(string $nom): void {
        if($nom == null) {
            throw new \InvalidArgumentException('nom ne peut être null');
        }
        if(strlen($nom) > 20) {
            throw new \InvalidArgumentException('nom doit être inférieur à 20 caractères');
        }

        $this->nom = $nom;
    }

    /**
     * Retourne le prénom du médecin
     * @return string Le prénom du médecin (20 caractères maximum)
     */
    public function getPrenom(): string {
        return $this->prenom;
    }

    /**
     * Modifie le prénom du médecin
     * @param string $prenom Le prénom du médecin (20 caractères maximum)
     * @throws \InvalidArgumentException Si le prénom est null ou supérieur à 20 caractères
     */
    public function setPrenom(string $prenom): void {
        if($prenom == null) {
            throw new \InvalidArgumentException('prenom ne peut être null');
        }
        if(strlen($prenom) > 20) {
            throw new \InvalidArgumentException('prenom doit être inférieur à 20 caractères');
        }

        $this->prenom = $prenom;
    }

}