<?php

namespace App\Controller;

use App\Model\UtilisateursModel;

/**
 * Super-contrôleur des pages nécessitant une connexion
 */
class RestrictedAccessController {

    /**
     * Méthode de contrôle de l'accès à la page
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté
     */
    public function restrictAccess(): void {
        if(!UtilisateursModel::isLogged()) {
            header('Location: ./');
            exit;
        }
    }

}