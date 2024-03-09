<?php

namespace App\Model;

use App\Entity\Utilisateur;

/**
 * Modèle des utilisateurs
 */
class UtilisateursModel {

    /**
     * Connexion d'un utilisateur
     * @param string $username Identifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return bool True si la connexion a réussi, false sinon
     * @throws \InvalidArgumentException Si l'identifiant ou le mot de passe sont vides
     * @throws \PDOException Si une erreur SQL survient
     */
    public function login(string $username, string $password): bool {
        $query = DBManager::getPDO()->prepare('SELECT * FROM utilisateurs WHERE identifiant = :identifiant');
        $query->execute([
            'identifiant' => $username
        ]);

        $user = $query->fetch(\PDO::FETCH_ASSOC);

        // Si l'utilisateur n'existe pas
        if(!$user) {
            return false;
        }

        // Si le mot de passe est incorrect
        if(!password_verify($password, $user['mot_de_passe'])) {
            return false;
        }

        // Si tout est OK, on enregistre l'utilisateur en session
        session_start();
        $_SESSION['user'] = $user;
        session_write_close();

        return true;
    }

    /**
     * Déconnexion de l'utilisateur
     * @return void
     */
    public function logout(): void {
        session_start();
        unset($_SESSION['user']);
        session_write_close();
    }

    /**
     * Vérifie si l'utilisateur est connecté
     * @return bool True si l'utilisateur est connecté, false sinon
     */
    public static function isLogged(): bool {
        // Si la session n'est pas démarrée, on la démarre
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
        session_write_close();
    }
    
    /**
     * Convertit le tableau data en objet Utilisateur
     * @param array $data Tableau contenant les données à convertir
     */
    public static function toEntity(array $data): Utilisateur {
        return new Utilisateur(
            $data['id_utilisateur'] ?: null,
            $data['identifiant'],
            $data['mot_de_passe']
        );
    }

}