<?php

namespace App\Controller;

use App\Model\StatistiquesModel;

/**
 * Contrôleur de la page des statistiques
 */
class StatistiquesController extends RestrictedAccessController {

    private StatistiquesModel $model;

    public function __construct() {
        $this->model = new StatistiquesModel();
    }

    /**
     * Méthode de contrôle de la page des statistiques
     * Statistiques de répartition des usagers et des médecins
     */
    public function statistiques() {
        // Vérification de l'accès à la page
        parent::restrictAccess();

        // Récupération des statistiques de répartition des usagers
        try {
            $statistiquesRepartition = $this->model->getStatistiquesRepartitionUsagers();
        } catch (\PDOException $e) {
            // Erreur fatale
            die('Une erreur est survenue : ' . $e->getMessage());
        }

        // Récupération des statistiques de répartition des médecins (filtrées ou non)
        try {
            $statistiquesMedecin = $this->model->getStatistiquesMedecins($_GET['filtre_recherche'] ?? '');
        } catch (\PDOException $e) {
            // Erreur fatale
            die('Une erreur est survenue : ' . $e->getMessage());
        }
        
        // Retourne les statistiques à la vue
        return [
            'statistiquesRepartition' => $statistiquesRepartition,
            'statistiquesMedecins' => $statistiquesMedecin,
        ];
    }

}