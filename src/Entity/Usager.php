<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Entity\Entity;
use App\Model\ListedExceptions;

/**
 * Class métier Usager
 */
class Usager implements Entity {

    private ?int $idUsager;
    private string $civilite;
    private string $nom;
    private string $prenom;
    private string $adresse;
    private string $ville;
    private string $codePostal;
    private DateTimeImmutable $dateNaissance;
    private string $villeNaissance;
    private string $codePostalNaissance;
    private string $numSecuriteSociale;
    private ?Medecin $medecin;

    /**
     * Constructeur Usager
     * @param int $idUsager Identifiant de l'usager (supérieur à 0)
     * @param string $civilite Civilité de l'usager (M ou Mme)
     * @param string $nom Nom de l'usager (20 caractères maximum)
     * @param string $prenom Prénom de l'usager (20 caractères maximum)
     * @param string $adresse Adresse de l'usager (50 caractères maximum)
     * @param string $ville Ville de l'usager (20 caractères maximum)
     * @param string $codePostal Code postal de l'usager (5 caractères)
     * @param string $dateNaissance Date de naissance de l'usager
     * @param string $villeNaissance Ville de naissance de l'usager (20 caractères maximum)
     * @param string $codePostalNaissance Code postal de naissance de l'usager (5 caractères)
     * @param string $numSecuriteSociale Numéro de sécurité sociale de l'usager (15 caractères maximum)
     * @param int|null $idMedecin Identifiant du médecin traitant de l'usager (null si aucun)
     * @throws \InvalidArgumentException Si un des champs n'est pas valide
     */
    public function __construct(?int $idUsager, string $civilite, string $nom, string $prenom, string $adresse, string $ville, string $codePostal, DateTimeImmutable $dateNaissance, string $villeNaissance, string $codePostalNaissance, string $numSecuriteSociale, ?Medecin $medecin) {
        $exceptions = new ListedExceptions();
        
        if (empty($civilite) || empty($nom) || empty($prenom) || empty($adresse) || empty($ville) || empty($codePostal) || empty($dateNaissance) || empty($villeNaissance) || empty($codePostalNaissance) || empty($numSecuriteSociale)) {
            $exceptions->addException(new \InvalidArgumentException('Tous les champs doivent être remplis'));
        }
        if($idUsager != null && $idUsager <= 0) {
            $exceptions->addException(new \InvalidArgumentException('idUsager doit être supérieur à 0'));
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
        if(strlen($adresse) > 50) {
            $exceptions->addException(new \InvalidArgumentException('L\'adresse doit être inférieure à 50 caractères'));
        }
        if(strlen($ville) > 50) {
            $exceptions->addException(new \InvalidArgumentException('La ville doit être inférieure à 50 caractères'));
        }
        if(strlen($codePostal) != 5 || !is_numeric($codePostal)) {
            $exceptions->addException(new \InvalidArgumentException('Le code postal doit contenir 5 chiffres'));
        }
        if($dateNaissance > new DateTimeImmutable()) {
            $exceptions->addException(new \InvalidArgumentException('La date de naissance ne peut être supérieure à maintenant'));
        }
        if($dateNaissance < new DateTimeImmutable('1900-01-01')) {
            $exceptions->addException(new \InvalidArgumentException('La date de naissance ne peut être inférieure à l\'an 1900'));
        }
        if(strlen($villeNaissance) > 50) {
            $exceptions->addException(new \InvalidArgumentException('La ville naissance doit être inférieure à 50 caractères'));
        }
        if(strlen($codePostalNaissance) != 5 && !is_numeric($codePostalNaissance)) {
            $exceptions->addException(new \InvalidArgumentException('Le code postal naissance doit contenir 5 chiffres'));
        }
        if(strlen($numSecuriteSociale) != 15 || !is_numeric($numSecuriteSociale)) {
            $exceptions->addException(new \InvalidArgumentException('Le numéro de sécurité sociale doit contenir 15 chiffres'));
        }

        if (!$exceptions->isEmpty()) {
            throw $exceptions;
        }

        $this->idUsager = $idUsager;
        $this->civilite = $civilite;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->codePostal = $codePostal;
        $this->dateNaissance = $dateNaissance;
        $this->villeNaissance = $villeNaissance;
        $this->codePostalNaissance = $codePostalNaissance;
        $this->numSecuriteSociale = $numSecuriteSociale;
        $this->medecin = $medecin;
    }

    /**
     * Getter de l'identifiant de l'usager
     * @return int Identifiant de l'usager
     */
    public function getIdUsager(): ?int {
        return $this->idUsager;
    }

    /**
     * Setter de l'identifiant de l'usager
     * @param int $idUsager Identifiant de l'usager (supérieur à 0)
     * @throws \InvalidArgumentException Si l'identifiant est null ou inférieur à 0
     */
    public function setIdUsager(int $idUsager): void {
        if($idUsager == null) {
            throw new \InvalidArgumentException('idUsager ne peut être null');
        }
        if($idUsager <= 0) {
            throw new \InvalidArgumentException('idUsager doit être supérieur à 0');
        }

        $this->idUsager = $idUsager;
    }

    /**
     * Getter de la civilité de l'usager
     * @return string Civilité de l'usager
     */
    public function getCivilite(): string {
        return $this->civilite;
    }

    /**
     * Setter de la civilité de l'usager
     * @param string $civilite Civilité de l'usager (M ou Mme)
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
     * Getter du nom de l'usager
     * @return string Nom de l'usager
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * Setter du nom de l'usager
     * @param string $nom Nom de l'usager (20 caractères maximum)
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
     * Getter du prénom de l'usager
     * @return string Prénom de l'usager
     */
    public function getPrenom(): string {
        return $this->prenom;
    }

    /**
     * Setter du prénom de l'usager
     * @param string $prenom Prénom de l'usager (20 caractères maximum)
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

    /**
     * Getter de l'adresse de l'usager
     * @return string Adresse de l'usager
     */
    public function getAdresse(): string {
        return $this->adresse;
    }

    /**
     * Setter de l'adresse de l'usager
     * @param string $adresse Adresse de l'usager (50 caractères maximum)
     * @throws \InvalidArgumentException Si l'adresse est null ou supérieure à 50 caractères
     */
    public function setAdresse(string $adresse): void {
        if($adresse == null) {
            throw new \InvalidArgumentException('adresse ne peut être null');
        }
        if(strlen($adresse) > 50) {
            throw new \InvalidArgumentException('adresse doit être inférieur à 50 caractères');
        }

        $this->adresse = $adresse;
    }

    /**
     * Getter de la ville de l'usager
     * @return string Ville de l'usager
     */
    public function getVille(): string {
        return $this->ville;
    }

    /**
     * Setter de la ville de l'usager
     * @param string $ville Ville de l'usager (50 caractères maximum)
     * @throws \InvalidArgumentException Si la ville est null ou supérieure à 50 caractères
     */
    public function setVille(string $ville): void {
        if($ville == null) {
            throw new \InvalidArgumentException('ville ne peut être null');
        }
        if(strlen($ville) > 50) {
            throw new \InvalidArgumentException('ville doit être inférieur à 50 caractères');
        }

        $this->ville = $ville;
    }

    /**
     * Getter du code postal de l'usager
     * @return string Code postal de l'usager
     */
    public function getCodePostal(): string {
        return $this->codePostal;
    }

    /**
     * Setter du code postal de l'usager
     * @param string $codePostal Code postal de l'usager (5 chiffres)
     * @throws \InvalidArgumentException Si le code postal est null ou longueur différente de 5 chiffres
     */
    public function setCodePostal(string $codePostal): void {
        if($codePostal == null) {
            throw new \InvalidArgumentException('codePostal ne peut être null');
        }
        if(strlen($codePostal) != 5 || !is_numeric($codePostal)) {
            throw new \InvalidArgumentException('codePostal doit contenir 5 chiffres');
        }

        $this->codePostal = $codePostal;
    }

    /**
     * Getter de la date de naissance de l'usager
     * @return DateTimeImmutable Date de naissance de l'usager
     */
    public function getDateNaissance(): DateTimeImmutable {
        return $this->dateNaissance;
    }

    /**
     * Setter de la date de naissance de l'usager
     * @param string $dateNaissance Date de naissance de l'usager
     * @throws \InvalidArgumentException Si la date de naissance est null
     */
    public function setDateNaissance(DateTimeImmutable $dateNaissance): void {
        if($dateNaissance == null) {
            throw new \InvalidArgumentException('dateNaissance ne peut être null');
        }

        $this->dateNaissance = $dateNaissance;
    }

    /**
     * Getter de la ville de naissance de l'usager
     * @return string Ville de naissance de l'usager
     */
    public function getVilleNaissance(): string {
        return $this->villeNaissance;
    }

    /**
     * Setter de la ville de naissance de l'usager
     * @param string $villeNaissance Ville de naissance de l'usager (50 caractères maximum)
     * @throws \InvalidArgumentException Si la ville de naissance est null ou supérieure à 50 caractères
     */
    public function setVilleNaissance(string $villeNaissance): void {
        if($villeNaissance == null) {
            throw new \InvalidArgumentException('villeNaissance ne peut être null');
        }
        if(strlen($villeNaissance) > 50) {
            throw new \InvalidArgumentException('villeNaissance doit être inférieur à 50 caractères');
        }

        $this->villeNaissance = $villeNaissance;
    }

    /**
     * Getter du code postal de naissance de l'usager
     * @return string Code postal de naissance de l'usager
     */
    public function getCodePostalNaissance(): string {
        return $this->codePostalNaissance;
    }

    /**
     * Setter du code postal de naissance de l'usager
     * @param string $codePostalNaissance Code postal de naissance de l'usager (5 chiffres)
     * @throws \InvalidArgumentException Si le code postal de naissance est null ou longueur différente de 5 chiffres
     */
    public function setCodePostalNaissance(string $codePostalNaissance): void {
        if($codePostalNaissance == null) {
            throw new \InvalidArgumentException('codePostalNaissance ne peut être null');
        }
        if(strlen($codePostalNaissance) != 5 || !is_numeric($codePostalNaissance)) {
            throw new \InvalidArgumentException('codePostalNaissance doit contenir 5 chiffres');
        }

        $this->codePostalNaissance = $codePostalNaissance;
    }

    /**
     * Getter du numéro de sécurité sociale de l'usager
     * @return string Numéro de sécurité sociale de l'usager
     */
    public function getNumSecuriteSociale(): string {
        return $this->numSecuriteSociale;
    }

    /**
     * Setter du numéro de sécurité sociale de l'usager
     * @param string $numSecuriteSociale Numéro de sécurité sociale de l'usager (15 chiffres)
     * @throws \InvalidArgumentException Si le numéro de sécurité sociale est null ou longueur différente de 15 chiffres
     */
    public function setNumSecuriteSociale(string $numSecuriteSociale): void {
        if($numSecuriteSociale == null) {
            throw new \InvalidArgumentException('numSecuriteSociale ne peut être null');
        }
        if(strlen($numSecuriteSociale) != 15 || !is_numeric($numSecuriteSociale)) {
            throw new \InvalidArgumentException('numSecuriteSociale doit contenir 15 chiffres');
        }

        $this->numSecuriteSociale = $numSecuriteSociale;
    }

    /**
     * Getter du médecin traitant de l'usager
     * @return Medecin Médecin traitant de l'usager
     */
    public function getMedecin(): ?Medecin {
        return $this->medecin;
    }

    /**
     * Setter du médecin traitant de l'usager
     * @param Medecin|null Médecin traitant de l'usager (null si aucun)
     */
    public function setMedecin(?Medecin $medecin): void {
        $this->medecin = $medecin;
    }

}