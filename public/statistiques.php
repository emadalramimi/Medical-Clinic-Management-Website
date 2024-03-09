<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\StatistiquesController;

$controller = new StatistiquesController;
$templateVariables = $controller->statistiques();
$statistiquesRepartition = $templateVariables['statistiquesRepartition'];
$statistiquesMedecins = $templateVariables['statistiquesMedecins'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('../src/Template/head.php'); ?>
    <title>Cabinet médical - Statistiques</title>
</head>
<body class="<?php include('../src/Template/nightModeBodyClass.php') ?>">
    <div class="page">
        <?php include('../src/Template/header.php'); ?>
        <main>
            <div class="wrapper">
                <h1>Statistiques du cabinet médical</h1>
                <h2>Répartition des usagers</h2>
                <table class="table-stylized">
                    <thead>
                        <tr>
                            <th>Tranche d'âge</th>
                            <th>Nombre d'hommes</th>
                            <th>Nombre de femmes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Moins de 25 ans</td>
                            <td><?= $statistiquesRepartition['hommes']['moins_25_ans'] ?></td>
                            <td><?= $statistiquesRepartition['femmes']['moins_25_ans'] ?></td>
                        </tr>
                        <tr>
                            <td>Entre 25 et 50 ans</td>
                            <td><?= $statistiquesRepartition['hommes']['entre_25_et_50_ans'] ?></td>
                            <td><?= $statistiquesRepartition['femmes']['entre_25_et_50_ans'] ?></td>
                        </tr>
                        <tr>
                            <td>Plus de 50 ans</td>
                            <td><?= $statistiquesRepartition['hommes']['plus_50_ans'] ?></td>
                            <td><?= $statistiquesRepartition['femmes']['plus_50_ans'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <h2>Nombre d'heures de consultation par médecin</h2>
                <p>Statistiques mises à jour en temps réel, prenant en compte uniquement les consultations passées.</p>
                <form class="filtre" action="statistiques.php" method="GET">
                    <input type="text" name="filtre_recherche" class="recherche" width="500" placeholder="Rechercher par nom et prénom ..." <?= isset($_GET['filtre_recherche']) ? 'value="' . $_GET['filtre_recherche'] . '"' : '' ?>>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-filter"></i>Filtrer</button>
                    <a class="btn btn-secondary" href="statistiques.php"><i class="bi bi-x"></i>Réinitialiser</a>
                </form>
                <table class="table-stylized">
                    <thead>
                        <tr>
                            <th>Nom, prénom</th>
                            <th>Temps en consultation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($statistiquesMedecins as $statistiqueMedecin): ?>
                            <tr>
                                <td>Dr. <?= $statistiqueMedecin['nom'] . ' ' . $statistiqueMedecin['prenom'] ?></td>
                                <td><?= ($statistiqueMedecin['nb_heures'] >= 60) ? floor($statistiqueMedecin['nb_heures'] / 60) . ' h ' . ($statistiqueMedecin['nb_heures'] % 60) . ' min' : $statistiqueMedecin['nb_heures'] . ' min'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <?php include('../src/Template/footer.php'); ?>
</body>
</html>