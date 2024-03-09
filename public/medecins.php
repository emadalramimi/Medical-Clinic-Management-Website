<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\MedecinsController;

$controller = new MedecinsController;
$templateVariables = $controller->medecins();
$action = $templateVariables['action'];
$medecin = $templateVariables['medecin'];
$medecins = $templateVariables['medecins'];
$errors = $templateVariables['errors'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('../src/Template/head.php'); ?>
    <title>Cabinet médical - Medecins</title>
</head>
<body class="<?php include('../src/Template/nightModeBodyClass.php') ?>">
    <div class="page">
        <?php include('../src/Template/header.php'); ?>
        <main>
            <div class="wrapper">
                <?php if($action == null): ?>
                    <h1>Liste des médecins</h1>
                    <?php if (isset($_GET['success']) && ($_GET['success'] == 'add' || $_GET['success'] == 'edit' || $_GET['success'] == 'delete')): ?>
                        <div class="alert alert-success">
                            Le médecin a bien été <?= $_GET['success'] == 'add' ? 'ajouté' : ($_GET['success'] == 'edit' ? 'modifié' : 'supprimé') ?>
                        </div>
                    <?php endif; ?>
                    <div class="row row-space-between">
                        <div class="col-left">
                            <form class="filtre" action="medecins.php" method="GET">
                                <input type="text" name="filtre_recherche" class="recherche" width="500" placeholder="Rechercher par nom et prénom ..." <?= isset($_GET['filtre_recherche']) ? 'value="' . $_GET['filtre_recherche'] . '"' : '' ?>>
                                <button class="btn btn-primary" type="submit"><i class="bi bi-filter"></i>Filtrer</button>
                                <a class="btn btn-secondary" href="medecins.php"><i class="bi bi-x"></i>Réinitialiser</a>
                            </form>
                        </div>
                        <div class="col-right">
                            <a class="btn btn-primary" href="medecins.php?action=add"><i class="bi bi-plus-circle"></i>Ajouter un médecin</a>
                        </div>
                    </div>
                    <?php if(count($medecins) > 0) { ?>
                        <table class="table-stylized">
                            <thead>
                                <tr>
                                    <th>Nom, prénom</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($medecins as $medecin): ?>
                                    <tr>
                                        <td><?= '(' . $medecin->getCivilite() . ') Dr. ' . $medecin->getNom() . ' ' . $medecin->getPrenom() ?></td>
                                        <td>
                                            <a class="action action-edit" title="Modifier l'usager" href="medecins.php?action=edit&id=<?= $medecin->getIdMedecin() ?>"><i class="bi bi-pencil-square"></i></a>
                                            <a class="action action-delete" title="Supprimer l'usager" href="medecins.php?action=delete&id=<?= $medecin->getIdMedecin() ?>"><i class="bi bi-trash3-fill"></i></a>
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
                    <a class="retour" href="medecins.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Ajouter un médecin</h1>
                    <div class="box align-center">
                        <?php if($errors != null): ?>
                            <div class="alert alert-danger">
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        <form action="medecins.php?action=add" method="POST">
                            <div class="row">
                                <div class="col col-2">
                                    <div class="form-row">
                                        <label for="civilite">Civilité</label>
                                        <select name="civilite" id="civilite" required>
                                            <option value="" disabled <?= !isset($_POST['civilite']) || empty($_POST['civilite']) ? 'selected' : '' ?>>-- Sélection --</option>
                                            <option value="M" <?= isset($_POST['civilite']) && $_POST['civilite'] == 'M' ? 'selected' : '' ?>>M</option>
                                            <option value="Mme" <?= isset($_POST['civilite']) && $_POST['civilite'] == 'Mme' ? 'selected' : '' ?>>Mme</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="nom">Nom</label>
                                        <input type="text" name="nom" id="nom" placeholder="Nom du médecin" <?= isset($_POST['nom']) ? 'value="' . $_POST['nom'] . '"' : '' ?> maxlength="20" required>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" name="prenom" id="prenom" placeholder="Prénom du médecin" <?= isset($_POST['prenom']) ? 'value="' . $_POST['prenom'] . '"' : '' ?> maxlength="20" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <button class="btn btn-primary align-center" type="submit">Ajouter le médecin</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($action == 'edit'): ?>
                    <a class="retour" href="medecins.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Modifier un médecin</h1>
                    <div class="box align-center">
                        <?php if($errors != null): ?>
                            <div class="alert alert-danger">
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        <form action="medecins.php?action=edit&id=<?= $medecin->getIdMedecin() ?>" method="POST">
                            <input type="hidden" name="id_medecin" value="<?= $medecin->getIdMedecin() ?>">
                            <div class="row">
                                <div class="col col-2">
                                    <div class="form-row">
                                        <label for="civilite">Civilité</label>
                                        <select name="civilite" id="civilite" required>
                                            <option value="" disabled selected>-- Sélection --</option>
                                            <option <?= ($medecin->getCivilite() == 'M') ? 'selected' : '' ?> value="M">M</option>
                                            <option <?= ($medecin->getCivilite() == 'Mme') ? 'selected' : '' ?> value="Mme">Mme</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="nom">Nom</label>
                                        <input type="text" name="nom" id="nom" value="<?= $medecin->getNom() ?>" placeholder="Nom du médecin" maxlength="20" required>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" name="prenom" id="prenom" value="<?= $medecin->getPrenom() ?>" placeholder="Prénom du médecin" maxlength="20" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <button class="btn btn-primary align-center" type="submit">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($action == 'delete'): ?>
                    <a class="retour" href="medecins.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Supprimer un médecin</h1>
                    <div class="box align-center action-delete">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Êtes-vous sûr de vouloir supprimer le médecin <strong><?= '(' . $medecin->getCivilite() . ') Dr. ' . $medecin->getNom() . ' ' . $medecin->getPrenom() ?></strong> ?<br>Veuillez noter que les usagers ayant ce médecin traitant verront ce dernier supprimé.<br>Toutes les consultations du médecin seront également supprimées.</span>
                        <div class="row align-center">
                            <?php if($errors != null): ?>
                                <div class="alert alert-danger">
                                    <?= $errors ?>
                                </div>
                            <?php else: ?>
                                <div class="col-left">
                                    <a class="btn btn-secondary" href="medecins.php">Annuler</a>
                                </div>
                                <div class="col-right">
                                    <form action="medecins.php?action=delete&id=<?= $medecin->getIdMedecin() ?>" method="POST">
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