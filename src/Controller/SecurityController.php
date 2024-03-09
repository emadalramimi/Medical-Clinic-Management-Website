<?php

namespace App\Controller;

use App\Model\UtilisateursModel;

/**
 * Contrôleur de la page de connexion/déconnexion
 */
class SecurityController {

    private UtilisateursModel $model;

    public function __construct() {
        $this->model = new UtilisateursModel();
    }

    /**
     * Méthode de contrôle de la page de connexion
     */
    public function login(): array {
        // Si l'utilisateur est déjà connecté, on le redirige vers la page des consultations
        if(UtilisateursModel::isLogged()) {
            header('Location: ./consultations.php');
            exit;
        }

        // Si le formulaire de connexion a été soumis
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['identifiant'];
            $password = $_POST['mot_de_passe'];

            // Vérification des champs, puis tentative de connexion
            if(empty($username) || empty($password)) {
                $errors = 'Veuillez remplir tous les champs';
            } else {
                if($this->model->login($username, $password)) {
                    header('Location: ./consultations.php');
                    exit;
                } else {
                    $errors = 'Identifiant ou mot de passe incorrects';
                }
            }
        }

        return [
            'errors' => $errors ?? null
        ];
    }

    /**
     * Méthode de déconnexion
     */
    public function logout(): void {
        if(UtilisateursModel::isLogged()) {
            $this->model->logout();
        }
        // Redirection vers la page de connexion
        header('Location: ./');
        exit;
    }

    /**
     * Méthode de vérification de la connexion de l'utilisateur
     */
    public function isLogged(): bool {
        return $this->model->isLogged();
    }

}