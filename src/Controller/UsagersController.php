<?php

namespace App\Controller;

use App\Model\ConsultationsModel;
use App\Model\UsagersModel;
use App\Model\MedecinsModel;
use App\Model\ListedExceptions;

/**
 * Contrôleur des usagers
 */
class UsagersController extends RestrictedAccessController {

    private UsagersModel $usagersModel;
    private MedecinsModel $medecinsModel;
    private ConsultationsModel $consultationsModel;

    public function __construct() {
        $this->usagersModel = new UsagersModel;
        $this->medecinsModel = new MedecinsModel;
        $this->consultationsModel = new ConsultationsModel;
    }
    
    /**
     * Méthode de contrôle de la page des usagers
     * Liste, ajout, modification et suppression des usagers
     */
    public function usagers(): array {
        // Vérification de l'accès à la page
        parent::restrictAccess();
        
        // Récupération de l'action à effectuer
        if(isset($_GET['action'])){
            $action = $_GET['action'];
        } else {
            $action = null;
        }

        // Récupération des usagers (filtrés ou non)
        if(isset($_GET['filtre_recherche']) && isset($_GET['filtre_medecin'])) {
            try {
                $usagers = $this->usagersModel->getByFiltrage($_GET['filtre_recherche'], $_GET['filtre_medecin']);
            } catch (\PDOException $e) {
                // Erreur fatale
                die('Une erreur est survenue : ' . $e->getMessage());
            }
        } else {
            try {
                $usagers = $this->usagersModel->getAll();
            } catch (\PDOException $e) {
                // Erreur fatale
                die('Une erreur est survenue : ' . $e->getMessage());
            }
        }

        // Récupération des médecins (pour la liste déroulante des filtres, et pour l'ajout/la modification)
        try {
            $medecins = $this->medecinsModel->getAll();
        } catch (\PDOException $e) {
            die('Une erreur est survenue : ' . $e->getMessage());
        }

        // Création d'une liste ListedExceptions pour gérer les exceptions
        $exceptions = new ListedExceptions();
        
        // Traitement différent selon l'action
        switch($action){

            // Modification d'un usager
            case 'edit':
                // Si l'identifiant de l'usager est fourni
                if(isset($_GET['id'])){
                    // Récupération de l'usager, affichage d'une erreur si l'usager n'existe pas
                    try {
                        $usager = $this->usagersModel->getById(['id_usager' => $_GET['id']]);
                    } catch (\Exception $e) {
                        // Erreur fatale
                        die('Erreur : cet usager n\'existe pas');
                    }

                    // Si le formulaire a été soumis
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        // Modification de l'usager, affichage d'une erreur si la modification échoue
                        try {
                            $this->usagersModel->edit(UsagersModel::toEntity($_POST));
                            header('Location: ./usagers.php?success=edit');
                            exit;
                        } catch (\Exception $e) {
                            $exceptions->addException($e);
                        }
                    }
                } else {
                    // Erreur fatale
                    die('Erreur : paramètre id manquant');
                }
                break;

            // Ajout d'un usager
            case 'add':
                // Si le formulaire a été soumis
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    // Ajout de l'usager, affichage d'une erreur si l'ajout échoue
                    try {
                        $this->usagersModel->add(UsagersModel::toEntity($_POST));
                        header('Location: ./usagers.php?success=add');
                    } catch (\Exception $e) {
                        $exceptions->addException($e);
                    }
                }
                break;

            // Suppression d'un usager
            case 'delete':
                // Si l'identifiant de l'usager est fourni
                if(isset($_GET['id'])){
                    // Récupération de l'usager, affichage d'une erreur si l'usager n'existe pas
                    try {
                        $usager = $this->usagersModel->getById(['id_usager' => $_GET['id']]);
                    } catch (\Exception $e) {
                        // Erreur fatale
                        die('Erreur : cet usager n\'existe pas');
                    }

                    // Si le formulaire a été soumis
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        // Suppression de l'usager, affichage d'une erreur si la suppression échoue
                        try {
                            $this->usagersModel->delete(['id_usager' => $_GET['id']]);
                            header('Location: ./usagers.php?success=delete');
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
            'usagers' => $usagers ?? null,
            'usager' => $usager ?? null,
            'medecins' => $medecins ?? null,
            'errors' => !$exceptions->isEmpty() ? $exceptions->formatExceptions() : null
        ];
    }

}