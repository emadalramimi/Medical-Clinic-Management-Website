<?php

namespace App\Controller;

use App\Model\ConsultationsModel;
use App\Model\UsagersModel;
use App\Model\MedecinsModel;
use App\Model\ListedExceptions;

/**
 * Contrôleur des consultations
 */
class ConsultationsController extends RestrictedAccessController {

    private ConsultationsModel $consultationsModel;
    private UsagersModel $usagersModel;
    private MedecinsModel $medecinsModel;

    public function __construct() {
        $this->consultationsModel = new ConsultationsModel();
        $this->usagersModel = new UsagersModel();
        $this->medecinsModel = new MedecinsModel();
    }

    /**
     * Méthode de contrôle de la page des consultations
     * Liste, ajout, modification et suppression des consultations
     */
    public function consultations(): array {
        // Vérification de l'accès à la page
        parent::restrictAccess();

        // Récupération de l'action à effectuer
        if(isset($_GET['action'])){
            $action = $_GET['action'];
        } else {
            $action = null;
        }

        // Récupération des consultations (filtrées ou non)
        try {
            if(isset($_GET['id_medecin']) && isset($_GET['creneaux'])) {
                $consultations = $this->consultationsModel->getByFiltrage($_GET['id_medecin'], $_GET['creneaux']);
            } else {
                $consultations = $this->consultationsModel->getAll();
            }
        } catch (\PDOException $e) {
            // Erreur fatale
            die('Une erreur est survenue : ' . $e->getMessage());
        }
        
        // Récupération des médecins (pour la liste déroulante des filtres, et pour l'ajout/la modification)
        try {
            $medecins = $this->medecinsModel->getAll();
        } catch (\PDOException $e) {
            // Erreur fatale
            die('Une erreur est survenue : ' . $e->getMessage());
        }

        // Récupération des usagers (pour l'ajout/la modification)
        if($action == 'add' || $action == 'edit') {
            try {
                $usagers = $this->usagersModel->getAll();
            } catch (\PDOException $e) {
                // Erreur fatale
                die('Une erreur est survenue : ' . $e->getMessage());
            }
        }
        
        // Création d'une liste ListedExceptions pour gérer les exceptions
        $exceptions = new ListedExceptions();
        
        // Traitement différent selon l'action
        switch($action){

            // Modification d'une consultation
            case 'edit':
                // Si les identifiants d'une consultation sont fournis
                if(isset($_GET['id_usager']) && isset($_GET['date_heure'])){
                    // Récupération de la consultation, affichage d'une erreur si elle n'existe pas
                    try {
                        $consultation = $this->consultationsModel->getById(['id_usager' => $_GET['id_usager'], 'date_heure' => $_GET['date_heure']]);
                    } catch (\Exception $e) {
                        die('Erreur : cette consultation n\'existe pas');
                    }

                    // Si le formulaire a été envoyé
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        // Modification de la consultation, affichage d'une erreur si elle n'a pas pu être modifiée
                        try {
                            $this->consultationsModel->edit(ConsultationsModel::toEntity($_POST));

                            // Redirection vers la page des consultations avec un message de succès de l'édition
                            header('Location: ./consultations.php?success=edit');
                        } catch (\Exception $e) {
                            $exceptions->addException($e);
                        }
                    }
                } else {
                    // Erreur fatale
                    die('Erreur : paramètres id_usager et/ou date_heure manquant(s)');
                }
                break;

            // Ajout d'une consultation
            case 'add':
                // Si le formulaire a été envoyé
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    // Vérification de la validité de la date et de l'heure de la consultation
                    if(new \DateTimeImmutable($_POST['date_heure']) < new \DateTimeImmutable()) {
                        $exceptions->addException(new \InvalidArgumentException('La date et l\'heure de la consultation ne peuvent pas être antérieures à la date et l\'heure actuelles'));
                    }

                    // Conversion des données du formulaire en objet Consultation
                    try {
                        $consultation = ConsultationsModel::toEntity($_POST);
                    } catch(\Exception $e) {
                        $exceptions->addException($e);
                    }

                    // Ajout de la consultation, affichage d'une erreur si elle n'a pas pu être ajoutée
                    if (isset($consultation) && $exceptions->isEmpty()) {
                        try {
                            $this->consultationsModel->add(ConsultationsModel::toEntity($_POST));

                            // Redirection vers la page des consultations avec un message de succès de l'ajout
                            header('Location: ./consultations.php?success=add');
                        } catch (\Exception $e) {
                            $exceptions->addException($e);
                        }
                    }
                }
                break;

            // Suppression d'une consultation
            case 'delete':
                // Si les identifiants d'une consultation sont fournis
                if(isset($_GET['id_usager']) && isset($_GET['date_heure'])){
                    // Récupération de la consultation, affichage d'une erreur si elle n'existe pas
                    try {
                        $consultation = $this->consultationsModel->getById(['id_usager' => $_GET['id_usager'], 'date_heure' => $_GET['date_heure']]);
                    } catch (\Exception $e) {
                        die('Erreur : cette consultation n\'existe pas');
                    }

                    // Suppression de la consultation, affichage d'une erreur si elle n'a pas pu être supprimée
                    if($_SERVER['REQUEST_METHOD'] == 'POST'){
                        try {
                            $this->consultationsModel->delete(['id_usager' => $_GET['id_usager'], 'date_heure' => $_GET['date_heure']]);

                            // Redirection vers la page des consultations avec un message de succès de la suppression
                            header('Location: ./consultations.php?success=delete');
                        } catch (\Exception $e) {
                            $exceptions->addException($e);
                        }
                    }
                } else {
                    // Erreur fatale
                    die('Erreur : paramètres id_usager et/ou date_heure manquant(s)');
                }
                break;
        }

        // Si l'action n'est pas valide
        if ($action != null && $action != 'edit' && $action != 'add' && $action != 'delete') {
            // Erreur fatale
            die('Erreur : action invalide');
        }

        // Passage des variables à la vue pour affichage
        return [
            'action' => $action,
            'consultation' => $consultation ?? null,
            'consultations' => $consultations,
            'usagers' => $usagers ?? null,
            'medecins' => $medecins ?? null,
            'errors' => !$exceptions->isEmpty() ? $exceptions->formatExceptions() : null
        ];
    }

}