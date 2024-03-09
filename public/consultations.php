<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\ConsultationsController;
use App\Controller\UsagersController;

$consultationsController = new ConsultationsController;
$usagersController = new UsagersController;
$templateVariables = $consultationsController->consultations();
$action = $templateVariables['action'];
$consultation = $templateVariables['consultation'];
$consultations = $templateVariables['consultations'];
$usagers = $templateVariables['usagers'];
$medecins = $templateVariables['medecins'];
$errors = $templateVariables['errors'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('../src/Template/head.php'); ?>
    <title>Cabinet médical - Consultations</title>
</head>
<body class="<?php include('../src/Template/nightModeBodyClass.php') ?>">
    <div class="page">
        <?php include('../src/Template/header.php'); ?>
        <main>
            <div class="wrapper">
                <?php if($action == null): ?>
                    <h1>Liste des consultations</h1>
                    <?php if (isset($_GET['success']) && ($_GET['success'] == 'add' || $_GET['success'] == 'edit' || $_GET['success'] == 'delete')): ?>
                        <div class="alert alert-success">
                            La consultation a bien été <?= $_GET['success'] == 'add' ? 'ajoutée' : ($_GET['success'] == 'edit' ? 'modifiée' : 'supprimée') ?>
                        </div>
                    <?php endif; ?>
                    <div class="row row-space-between">
                        <div class="col-left">
                            <form class="filtre" action="consultations.php" method="GET">
                                <select name="id_medecin" id="id_medecin">
                                    <option value="" <?= !isset($_GET['id_medecin']) || $_GET['id_medecin'] == '' ? 'selected' : '' ?>>Tous les médecins</option>
                                    <?php foreach($medecins as $medecin): ?>
                                        <option value="<?= $medecin->getIdMedecin() ?>"<?= isset($_GET['id_medecin']) && $_GET['id_medecin'] == $medecin->getIdMedecin() ? " selected" : '' ?>>Dr. <?= $medecin->getNom() . ' ' . $medecin->getPrenom() ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="creneaux" id="creneaux">
                                    <option value="" <?= !isset($_GET['creneaux']) || $_GET['creneaux'] == '' ? 'selected' : '' ?>>Tous les créneaux</option>
                                    <option value="passes" <?= isset($_GET['creneaux']) && $_GET['creneaux'] == 'passes' ? 'selected' : '' ?>>Créneaux passés</option>
                                    <option value="jour" <?= isset($_GET['creneaux']) && $_GET['creneaux'] == 'jour' ? 'selected' : '' ?>>Créneaux du jour</option>
                                    <option value="futurs" <?= isset($_GET['creneaux']) && $_GET['creneaux'] == 'futurs' ? 'selected' : '' ?>>Créneaux futurs</option>
                                </select>
                                <button class="btn btn-primary" type="submit"><i class="bi bi-filter"></i>Filtrer</button>
                                <a class="btn btn-secondary" href="consultations.php"><i class="bi bi-x"></i>Réinitialiser</a>
                            </form>
                        </div>
                        <div class="col-right">
                            <a class="btn btn-primary" href="consultations.php?action=add"><i class="bi bi-plus-circle"></i>Nouvelle consultation</a>
                        </div>
                    </div>
                    <?php if(count($consultations) > 0) { ?>
                        <table class="table-stylized">
                            <thead>
                                <tr>
                                    <th>Médecin</th>
                                    <th>Usager</th>
                                    <th>Date consultation</th>
                                    <th>Durée consultation</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($consultations as $consultation): ?>
                                    <tr>
                                        <td>Dr. <?= $consultation->getMedecin()->getNom() . ' ' . $consultation->getMedecin()->getPrenom() ?></td>
                                        <td><?= $consultation->getUsager()->getCivilite() . '. ' . $consultation->getUsager()->getNom() . ' ' . $consultation->getUsager()->getPrenom(); ?></td>
                                        <td><?= $consultation->getDateHeure()->format("d/m/Y H:i") ?></td>
                                        <td><?= ($consultation->getDuree() >= 60) ? floor($consultation->getDuree() / 60) . ' h ' . ($consultation->getDuree() % 60) . ' min' : $consultation->getDuree() . ' min'; ?></td>
                                        <td>
                                            <a class="action action-edit" href="consultations.php?action=edit&id_usager=<?= $consultation->getUsager()->getIdUsager() ?>&date_heure=<?= $consultation->getDateHeure()->format("Y-m-d H:i") ?>"><i class="bi bi-pencil-square"></i></a>
                                            <a class="action action-delete" href="consultations.php?action=delete&id_usager=<?= $consultation->getUsager()->getIdUsager() ?>&date_heure=<?= $consultation->getDateHeure()->format("Y-m-d H:i") ?>"><i class="bi bi-trash3-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php 
                        } else {
                            include('../src/Template/emptyList.php');
                        }
                    ?>
                <?php elseif($action == 'add'): ?>
                    <a class="retour" href="consultations.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Nouvelle consultation</h1>
                    <div class="box">
                        <?php if($errors != null): ?>
                            <div class="alert alert-danger">
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        <form action="consultations.php?action=add" method="POST">
                            <div class="row">
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="id_usager">Usager</label>
                                        <select name="id_usager" id="id_usager" required>
                                            <option value="" disabled <?= !isset($_POST['id_usager']) || empty($_POST['id_usager']) ? 'selected' : '' ?>>-- Sélection --</option>
                                            <?php foreach($usagers as $usager): ?>
                                                <option value="<?= $usager->getIdUsager() ?>" <?= $usager->getMedecin() != null ? 'data-medecin-traitant="' . $usager->getMedecin()->getIdMedecin() . '"' : '' ?> <?= isset($_POST['id_usager']) && $_POST['id_usager'] == $usager->getIdUsager() ? 'selected' : '' ?>><?= $usager->getCivilite() . '. ' . $usager->getNom() . ' ' . $usager->getPrenom() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="id_medecin">Médecin</label>
                                        <select name="id_medecin" id="id_medecin" required>
                                            <option value="" disabled <?= !isset($_POST['id_medecin']) || empty($_POST['id_medecin']) ? 'selected' : '' ?>>-- Sélection --</option>
                                            <?php foreach($medecins as $medecin): ?>
                                                <option value="<?= $medecin->getIdMedecin() ?>" <?= isset($_POST['id_medecin']) && !empty($_POST['id_medecin']) && $_POST['id_medecin'] == $medecin->getIdMedecin() ? 'selected' : '' ?>>Dr. <?= $medecin->getNom() . ' ' . $medecin->getPrenom() ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="date_heure">Date et heure</label>
                                        <input type="datetime-local" name="date_heure" id="date_heure" <?= isset($_POST['date_heure']) && !empty($_POST['date_heure']) ? 'value="' . $_POST['date_heure'] . '"' : '' ?> required>
                                    </div>
                                </div>
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="duree">Durée</label>
                                        <input type="number" name="duree" id="duree" min="0" max="120" placeholder="Durée de la consultation en minutes" <?= isset($_POST['duree']) && !empty($_POST['duree']) ? 'value="' . $_POST['duree'] . '"' : '' ?> required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <button class="btn btn-primary align-center" type="submit">Créer la consultation</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($action == 'edit'): ?>
                    <a class="retour" href="consultations.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Modifier une consultation</h1>
                    <div class="box">
                        <?php if($errors != null): ?>
                            <div class="alert alert-danger">
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        <form action="consultations.php?action=edit&id_usager=<?= $consultation->getUsager()->getIdUsager() ?>&date_heure=<?= $consultation->getDateHeure()->format("Y-m-d H:i") ?>" method="POST">
                            <input type="hidden" name="id_usager_old" value="<?= $consultation->getIdUsagerOld(); ?>">
                            <input type="hidden" name="date_heure_old" value="<?= $consultation->getDateHeureOld()->format("Y-m-d H:i") ?>">
                            <div class="row">
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="id_usager">Usager</label>
                                        <select name="id_usager" id="id_usager" required>
                                            <option value="" disabled>-- Sélection --</option>
                                            <?php foreach ($usagers as $usager): ?>
                                                <option <?= ($consultation->getUsager()->getIdUsager() == $usager->getIdUsager()) ? 'selected' : '' ?> value="<?= $usager->getIdUsager() ?>"<?= $usager->getMedecin() != null ? ' data-medecin-traitant="' . $usager->getMedecin()->getIdMedecin() . '"' : '' ?>><?= $usager->getCivilite() . '. ' . $usager->getNom() . ' ' . $usager->getPrenom() ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="id_medecin">Médecin</label>
                                        <select name="id_medecin" id="id_medecin" required>
                                            <option value="" disabled>-- Sélection --</option>
                                            <?php foreach ($medecins as $medecin): ?>
                                                <option <?= ($consultation->getMedecin()->getIdMedecin() == $medecin->getIdMedecin()) ? 'selected' : '' ?> value="<?= $medecin->getIdMedecin() ?>">Dr. <?= $medecin->getNom() . ' ' . $medecin->getPrenom() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="date_heure">Date et heure</label>
                                        <input type="datetime-local" name="date_heure" id="date_heure" value="<?= $consultation->getDateHeure()->format("Y-m-d\TH:i") ?>" required>
                                    </div>
                                </div>
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="duree">Durée</label>
                                        <input type="number" name="duree" id="duree" value="<?= $consultation->getDuree() ?>" min="0" max="120" placeholder="Durée de la consultation en minutes" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <button class="btn btn-primary align-center" type="submit">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($action == 'delete'): ?>
                    <a class="retour" href="consultations.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Supprimer une consultation</h1>
                    <div class="box align-center action-delete">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Êtes-vous sûr de vouloir supprimer la consultation de <strong><?= $consultation->getUsager()->getCivilite() . '. ' . $consultation->getUsager()->getNom() . ' ' . $consultation->getUsager()->getPrenom() ?></strong> du <strong><?= $consultation->getDateHeure()->format("d/m/Y à H:i") ?></strong> ?</span>
                        <div class="row align-center">
                            <?php if($errors != null): ?>
                                <div class="alert alert-danger">
                                    <?= $errors ?>
                                </div>
                            <?php else: ?>
                                <div class="col-left">
                                    <a class="btn btn-secondary" href="consultations.php">Annuler</a>
                                </div>
                                <div class="col-right">
                                    <form action="consultations.php?action=delete&id_usager=<?= $consultation->getUsager()->getIdUsager() ?>&date_heure=<?= $consultation->getDateHeure()->format("Y-m-d H:i") ?>" method="POST">
                                        <button class="btn btn-danger" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php include('../src/Template/footer.php'); ?>
</body>
</html>