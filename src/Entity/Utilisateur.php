<?php

namespace App\Entity;

/**
 * Class métier Utilisateur
 */
class Utilisateur implements Entity {

    private int $idUtilisateur;
    private string $identifiant;
    private string $motDePasse;

    /**
     * Constructeur de la classe Utilisateur
     * @param int|null $idUtilisateur L'identifiant de l'utilisateur (null si nouveau utilisateur)
     * @param string $identifiant L'identifiant de l'utilisateur (20 caractères maximum)
     * @param string $motDePasse Le mot de passe de l'utilisateur (20 caractères maximum)
     * @throws \InvalidArgumentException Si un des paramètres est invalide
     */
    public function __construct(?int $idUtilisateur, string $identifiant, string $motDePasse) {
        if($identifiant == null || $motDePasse == null) {
            throw new \InvalidArgumentException('L\'identifiant et le mot de passe ne peuvent pas être nuls');
        }
        if($idUtilisateur != null && $idUtilisateur <= 0) {
            throw new \InvalidArgumentException('L\'id de l\'utilisateur doit être un entier positif');
        }

        $this->idUtilisateur = $idUtilisateur;
        $this->identifiant = $identifiant;
        $this->motDePasse = $motDePasse;
    }
    
    /**
     * Retourne l'identifiant de l'utilisateur
     * @return ?int L'identifiant de l'utilisateur
     */
    public function getIdUtilisateur(): ?int {
        return $this->idUtilisateur;
    }

    /**
     * Modifie l'identifiant de l'utilisateur
     * @param int $idUtilisateur L'identifiant de l'utilisateur
     * @throws \InvalidArgumentException Si l'identifiant est invalide
     */
    public function setIdUtilisateur(int $idUtilisateur): void {
        if($idUtilisateur <= 0) {
            throw new \InvalidArgumentException('L\'id de l\'utilisateur doit être un entier positif');
        }

        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Retourne l'identifiant de l'utilisateur
     * @return string L'identifiant de l'utilisateur
     */
    public function getIdentifiant(): string {
        return $this->identifiant;
    }

    /**
     * Modifie l'identifiant de l'utilisateur
     * @param string $identifiant L'identifiant de l'utilisateur
     * @throws \InvalidArgumentException Si l'identifiant est invalide
     */
    public function setIdentifiant(string $identifiant): void {
        if($identifiant == null) {
            throw new \InvalidArgumentException('L\'identifiant ne peut pas être nul');
        }

        $this->identifiant = $identifiant;
    }

    /**
     * Retourne le mot de passe chiffré de l'utilisateur
     * @return string Le mot de passe chiffré de l'utilisateur
     */
    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    /**
     * Modifie le mot de passe de l'utilisateur (pas de chiffrement ici, c'est le rôle du modèle)
     * @param string $motDePasse Le mot de passe de l'utilisateur
     * @throws \InvalidArgumentException Si le mot de passe est invalide
     */
    public function setMotDePasse(string $motDePasse): void {
        if($motDePasse == null) {
            throw new \InvalidArgumentException('Le mot de passe ne peut pas être nul');
        }

        $this->motDePasse = $motDePasse;
    }

}