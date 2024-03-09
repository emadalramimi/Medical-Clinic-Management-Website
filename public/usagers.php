<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\UsagersController;

$controller = new UsagersController;
$templateVariables = $controller->usagers();
$action = $templateVariables['action'];
$usager = $templateVariables['usager'];
$usagers = $templateVariables['usagers'];
$medecins = $templateVariables['medecins'];
$errors = $templateVariables['errors'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('../src/Template/head.php'); ?>
    <title>Cabinet médical - Usagers</title>
</head>
<body class="<?php include('../src/Template/nightModeBodyClass.php') ?>">
    <div class="page">
        <?php include('../src/Template/header.php'); ?>
        <main>
            <div class="wrapper">
                <?php if($action == null): ?>
                    <h1>Liste des usagers</h1>
                    <?php if (isset($_GET['success']) && ($_GET['success'] == 'add' || $_GET['success'] == 'edit' || $_GET['success'] == 'delete')): ?>
                        <div class="alert alert-success">
                            L'usager a bien été <?= $_GET['success'] == 'add' ? 'ajouté' : ($_GET['success'] == 'edit' ? 'modifié' : 'supprimé') ?>
                        </div>
                    <?php endif; ?>
                    <div class="row row-space-between">
                        <div class="col-left">
                            <form class="filtre" action="usagers.php" method="GET">
                                <input type="text" name="filtre_recherche" class="recherche" width="500" placeholder="Rechercher par nom et prénom ..." <?= isset($_GET['filtre_recherche']) ? 'value="' . $_GET['filtre_recherche'] . '"' : '' ?>>
                                <select name="filtre_medecin" id="filtre_medecin">
                                    <option value="" <?= !isset($_GET['filtre_medecin']) || empty($_GET['filtre_medecin']) ? 'selected' : '' ?>>Tous les médecins</option>
                                    <?php foreach($medecins as $medecin): ?>
                                        <option value="<?= $medecin->getIdMedecin() ?>" <?= isset($_GET['filtre_medecin']) && $_GET['filtre_medecin'] == $medecin->getIdMedecin() ? 'selected' : '' ?>>Dr. <?= $medecin->getNom() . ' ' . $medecin->getPrenom() ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-primary" type="submit"><i class="bi bi-filter"></i>Filtrer</button>
                                <a class="btn btn-secondary" href="usagers.php"><i class="bi bi-x"></i>Réinitialiser</a>
                            </form>
                        </div>
                        <div class="col-right">
                            <a class="btn btn-primary" href="usagers.php?action=add"><i class="bi bi-plus-circle"></i>Ajouter un usager</a>
                        </div>
                    </div>
                    <?php if(count($usagers) > 0) { ?>
                        <table class="table-stylized">
                            <thead>
                                <tr>
                                    <th>Nom, prénom</th>
                                    <th>Adresse</th>
                                    <th>Date et ville de naissance</th>
                                    <th>Médecin traitant</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($usagers as $usager) { ?>
                                    <tr>
                                        <td><?= $usager->getCivilite() . '. ' . $usager->getNom() . ' ' . $usager->getPrenom() ?></td>
                                        <td><?= $usager->getAdresse() . ',<br> ' . $usager->getCodePostal() . ' ' . $usager->getVille() ?></td>
                                        <td><?= $usager->getDateNaissance()->format('d/m/Y') . '<br> '. $usager->getCodePostalNaissance() . ' ' . $usager->getVilleNaissance() ?></td>
                                        <td><?= $usager->getMedecin() != null ? 'Dr. ' . $usager->getMedecin()->getNom() . ' ' . $usager->getMedecin()->getPrenom() : "Aucun" ?></td>
                                        <td>
                                            <a class="action action-edit" title="Modifier l'usager" href="usagers.php?action=edit&id=<?= $usager->getIdUsager() ?>"><i class="bi bi-pencil-square"></i></a>
                                            <a class="action action-delete" title="Supprimer l'usager" href="usagers.php?action=delete&id=<?= $usager->getIdUsager() ?>"><i class="bi bi-trash3-fill"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php 
                        } else {
                            include('../src/Template/emptyList.php');
                        }
                    ?>
                <?php elseif($action == 'add'): ?>
                    <a class="retour" href="usagers.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Ajouter un usager</h1>
                    <div class="box align-center">
                        <?php if($errors != null): ?>
                            <div class="alert alert-danger">
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        <form action="usagers.php?action=add" method="POST">
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
                                        <input type="text" name="nom" id="nom" placeholder="Nom de l'usager" <?= isset($_POST['nom']) ? 'value="' . $_POST['nom'] . '"' : '' ?> maxlength="20" required>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" name="prenom" id="prenom" placeholder="Prénom de l'usager" <?= isset($_POST['prenom']) ? 'value="' . $_POST['prenom'] . '"' : '' ?> maxlength="20" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="adresse">Adresse</label>
                                <input type="text" name="adresse" id="adresse" placeholder="Adresse postale de l'usager" <?= isset($_POST['adresse']) ? 'value="' . $_POST['adresse'] . '"' : '' ?> maxlength="50" required>
                            </div>
                            <div class="cp_ville_auto">
                                <div class="row">
                                    <div class="col col-3">
                                        <div class="form-row">
                                            <label for="code_postal">Code postal</label>
                                            <input type="number" name="code_postal" id="code_postal" class="code_postal" placeholder="Code postal de l'usager" <?= isset($_POST['code_postal']) ? 'value="' . $_POST['code_postal'] . '"' : '' ?> minlength="5" maxlength="5" required>
                                        </div>
                                    </div>
                                    <div class="col col-7">
                                        <div class="form-row">
                                            <label for="ville">Ville</label>
                                            <select name="ville" id="ville" class="ville" required <?= isset($_POST['ville']) ? 'data-selected="' . $_POST['ville'] . '"' : '' ?>></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="date_naissance">Date de naissance</label>
                                <input type="date" name="date_naissance" id="date_naissance" placeholder="Date de naissance de l'usager" <?= isset($_POST['date_naissance']) ? 'value="' . $_POST['date_naissance'] . '"' : '' ?> required>
                            </div>
                            <div class="cp_ville_auto">
                                <div class="row">
                                    <div class="col col-3">
                                        <div class="form-row">
                                            <label for="code_postal_naissance">Code postal de naissance</label>
                                            <input type="number" name="code_postal_naissance" id="code_postal_naissance" class="code_postal" placeholder="Code postal de naissance de l'usager" <?= isset($_POST['code_postal_naissance']) ? 'value="' . $_POST['code_postal_naissance'] . '"' : '' ?> minlength="5" maxlength="5" required>
                                        </div>
                                    </div>
                                    <div class="col col-7">
                                        <div class="form-row">
                                            <label for="ville_naissance">Ville de naissance</label>
                                            <select name="ville_naissance" id="ville_naissance" class="ville" required <?= isset($_POST['ville_naissance']) ? 'data-selected="' . $_POST['ville_naissance'] . '"' : '' ?>></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="num_securite_sociale">Numéro de sécurité sociale</label>
                                        <input type="number" name="num_securite_sociale" id="num_securite_sociale" placeholder="185057800608436" <?= isset($_POST['num_securite_sociale']) ? 'value="' . $_POST['num_securite_sociale'] . '"' : '' ?> minlength="15" maxlength="15" required>
                                    </div>
                                </div>
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="id_medecin">Médecin traitant</label>
                                        <select name="id_medecin" id="id_medecin">
                                            <option value="" <?= !isset($_POST['id_medecin']) || empty($_POST['id_medecin']) ? 'selected' : '' ?>>-- Aucun --</option>
                                            <?php foreach($medecins as $medecin): ?>
                                                <option value="<?= $medecin->getIdMedecin() ?>" <?= isset($_POST['id_medecin']) && $_POST['id_medecin'] == $medecin->getIdMedecin() ? 'selected' : '' ?>>Dr. <?= $medecin->getNom() . ' ' . $medecin->getPrenom() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <button class="btn btn-primary align-center" type="submit">Ajouter l'usager</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($action == 'edit'): ?>
                    <a class="retour" href="usagers.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Modifier un usager</h1>
                    <div class="box align-center">
                        <?php if($errors != null): ?>
                            <div class="alert alert-danger">
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        <form action="usagers.php?action=edit&id=<?= $usager->getIdUsager() ?>" method="POST">
                            <input type="hidden" name="id_usager" value="<?= $usager->getIdUsager() ?>">
                            <div class="row">
                                <div class="col col-2">
                                    <div class="form-row">
                                        <label for="civilite">Civilité</label>
                                        <select name="civilite" id="civilite" required>
                                            <option value="" disabled>-- Sélection --</option>
                                            <option <?= ($usager->getCivilite() == 'M') ? 'selected' : '' ?> value="M">M</option>
                                            <option <?= ($usager->getCivilite() == 'Mme') ? 'selected' : '' ?> value="Mme">Mme</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="nom">Nom</label>
                                        <input type="text" name="nom" id="nom" value="<?= $usager->getNom() ?>" maxlength="20" required>
                                    </div>
                                </div>
                                <div class="col col-4">
                                    <div class="form-row">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" name="prenom" id="prenom" value="<?= $usager->getPrenom() ?>" maxlength="20" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="adresse">Adresse</label>
                                <input type="text" name="adresse" id="adresse" value="<?= $usager->getAdresse() ?>" maxlength="50" required>
                            </div>
                            <div class="cp_ville_auto">
                                <div class="row">
                                    <div class="col col-3">
                                        <div class="form-row">
                                            <label for="code_postal">Code postal</label>
                                            <input type="number" name="code_postal" id="code_postal" class="code_postal" value="<?= $usager->getCodePostal() ?>" minlength="5" maxlength="5" required>
                                        </div>
                                    </div>
                                    <div class="col col-7">
                                        <div class="form-row">
                                            <label for="ville">Ville</label>
                                            <select name="ville" id="ville" class="ville" required data-selected="<?= $usager->getVille() ?>"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="date_naissance">Date de naissance</label>
                                <input type="date" name="date_naissance" id="date_naissance" value="<?= $usager->getDateNaissance()->format("Y-m-d") ?>" required>
                            </div>
                            <div class="cp_ville_auto">
                                <div class="row">
                                    <div class="col col-3">
                                        <div class="form-row">
                                            <label for="code_postal_naissance">Code postal de naissance</label>
                                            <input type="number" name="code_postal_naissance" id="code_postal_naissance" class="code_postal" value="<?= $usager->getCodePostalNaissance() ?>" minlength="5" maxlength="5" required>
                                        </div>
                                    </div>
                                    <div class="col col-7">
                                        <div class="form-row">
                                            <label for="ville_naissance">Ville de naissance</label>
                                            <select name="ville_naissance" id="ville_naissance" class="ville" required data-selected="<?= $usager->getVilleNaissance() ?>"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="num_securite_sociale">Numéro de sécurité sociale</label>
                                        <input type="number" name="num_securite_sociale" id="num_securite_sociale" value="<?= $usager->getNumSecuriteSociale() ?>" minlength="15" maxlength="15" required>
                                    </div>
                                </div>
                                <div class="col col-5">
                                    <div class="form-row">
                                        <label for="id_medecin">Médecin traitant</label>
                                        <select name="id_medecin" id="id_medecin">
                                            <option value="" selected>-- Aucun --</option>
                                            <?php foreach ($medecins as $medecin): ?>
                                                <option <?= ($usager->getMedecin() && $usager->getMedecin()->getIdMedecin() == $medecin->getIdMedecin()) ? 'selected' : '' ?> value="<?= $medecin->getIdMedecin() ?>">Dr. <?= $medecin->getNom() . ' ' . $medecin->getPrenom() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <button class="btn btn-primary align-center" type="submit">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                <?php elseif ($action == 'delete'): ?>
                    <a class="retour" href="usagers.php"><i class="bi bi-arrow-left"></i> Retour</a>
                    <h1>Supprimer un usager</h1>
                    <div class="box align-center action-delete">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Êtes-vous sûr de vouloir supprimer l'usager <strong><?= $usager->getCivilite() . '. ' . $usager->getNom() . ' ' . $usager->getPrenom() ?></strong> ?<br>Toutes ses consultations seront également supprimées.</span>
                        <div class="row align-center">
                            <?php if($errors != null): ?>
                                <div class="alert alert-danger">
                                    <?= $errors ?>
                                </div>
                            <?php else: ?>
                                <div class="col-left">
                                    <a class="btn btn-secondary" href="usagers.php">Annuler</a>
                                </div>
                                <div class="col-right">
                                    <form action="usagers.php?action=delete&id=<?= $usager->getIdUsager() ?>" method="POST">
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