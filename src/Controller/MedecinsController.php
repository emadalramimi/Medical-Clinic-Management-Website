<?php

namespace App\Controller;

use App\Model\MedecinsModel;
use App\Model\ConsultationsModel;
use App\Model\ListedExceptions;

/**
 * Contrôleur des médecins
 */
class MedecinsController extends RestrictedAccessController {

    private MedecinsModel $model;
    private ConsultationsModel $consultationsModel;

    public function __construct() {
        $this->model = new MedecinsModel;
        $this->consultationsModel = new ConsultationsModel;
    }

    /**
     * Méthode de contrôle de la page des médecins
     * Liste, ajout, modification et suppression des médecins
     */
    public function medecins() {
        // Vérification de l'accès à la page
        parent::restrictAccess();
        
        // Récupération de l'action à effectuer
        if(isset($_GET['action'])){
            $action = $_GET['action'];
        } else {
            $action = null;
        }

        // Récupération des médecins (filtrés ou non)
        if(isset($_GET['filtre_recherche'])) {
            try {
                $medecins = $this->model->getByFiltrage($_GET['filtre_recherche']);
            } catch (\PDOException $e) {
                // Erreur fatale
                die('Une erreur est survenue : ' . $e->getMessage());
            }
        } else {
            try {
                $medecins = $this->model->getAll();
            } catch (\PDOException $e) {
                // Erreur fatale
                die('Une erreur est survenue : ' . $e->getMessage());
            }
        }
        
        // Création d'une liste ListedExceptions pour gérer les exceptions
        $exceptions = new ListedExceptions();

        // Traitement différent selon l'action
        switch($action){

            // Modification d'un médecin
            case 'edit':
                // Si l'id du médecin à modifier est passé en paramètre
                if(isset($_GET['id'])){
                    // Récupération du médecin
                    try {
                        $medecin = $this->model->getById(['id_medecin' => $_GET['id']]);
                    } catch (\Exception $e) {
                        // Erreur fatale
                        die('Erreur : ce médecin n\'existe pas');
                    }
                    
                    // Si le formulaire a été soumis
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        // Modification du médecin, affichage d'une erreur si la modification échoue
                        try {
                            $this->model->edit(MedecinsModel::toEntity($_POST));

                            // Redirection vers la page des médecins avec un message de succès de la modification
                            header('Location: ./medecins.php?success=edit');
                        } catch (\Exception $e) {
                            $exceptions->addException($e);
                        }
                    }
                } else {
                    // Erreur fatale
                    die('Erreur : paramètre id manquant');
                }
                break;

            // Ajout d'un médecin
            case 'add':
                // Si le formulaire a été soumis
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    // Ajout du médecin, affichage d'une erreur si l'ajout échoue
                    try {
                        $this->model->add(MedecinsModel::toEntity($_POST));

                        // Redirection vers la page des médecins avec un message de succès de l'ajout
                        header('Location: ./medecins.php?success=add');
                    } catch (\Exception $e) {
                        $exceptions->addException($e);
                    }
                }
                break;

            // Suppression d'un médecin
            case 'delete':
                // Si l'id du médecin à supprimer est passé en paramètre
                if(isset($_GET['id'])){
                    // Récupération du médecin, affichage d'une erreur si le médecin n'existe pas
                    try {
                        $medecin = $this->model->getById(['id_medecin' => $_GET['id']]);
                    } catch (\Exception $e) {
                        // Erreur fatale
                        die('Erreur : ce médecin n\'existe pas');
                    }

                    // Si le formulaire a été soumis
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        // Suppression du médecin, affichage d'une erreur si la suppression échoue
                        try {
                            $this->model->delete(['id_medecin' => $_GET['id']]);

                            // Redirection vers la page des médecins avec un message de succès de la suppression
                            header('Location: ./medecins.php?success=delete');
                        } catch (\Exception $e) {
                            $exceptions->addException($e);
                        }
                    }
                } else {
                    // Erreur fatale
                    die('Erreur : paramètre id manquant');
                }
                break;
        }

        // Si l'action n'est pas valide
        if ($action != null && $action != 'edit' && $action != 'add' && $action != 'delete') {
            die('Erreur : action invalide');
        }
        
        // Passage des variables à la vue pour affichage
        return [
            'action' => $action,
            'medecin' => $medecin ?? null,
            'medecins' => $medecins,
            'errors' => !$exceptions->isEmpty() ? $exceptions->formatExceptions() : null
        ];
    }

}