<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\SecurityController;

$controller = new SecurityController();
$templateVariables = $controller->login();
$errors = $templateVariables['errors'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('../src/Template/head.php'); ?>
    <title>Cabinet médical - Connexion</title>
</head>
<body class="login <?php include('../src/Template/nightModeBodyClass.php') ?>">
    <div class="page">
        <?php include('../src/Template/header.php'); ?>
        <main>
            <div class="wrapper">
                <div class="box align-center">
                    <h1>Connexion</h1>
                    <?php if($errors != null): ?>
                        <div class="alert alert-danger">
                            <?= $errors ?>
                        </div>
                    <?php endif; ?>
                    <form class="contact-form" method="POST">
                        <div class="form-row">
                            <label for="identifiant">Identifiant</label>
                            <input type="text" name="identifiant" id="identifiant" placeholder="Entrez votre identifiant" <?= isset($_POST['identifiant']) && !empty($_POST['identifiant']) ? 'value="' . $_POST['identifiant'] . '"' : '' ?> required>
                        </div>
                        <div class="form-row">
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Entrez votre mot de passe" required>
                        </div>
                        <div class="form-row">
                            <button class="btn btn-primary align-center" type="submit">Se connecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <?php include('../src/Template/footer.php'); ?>
</body>
</html>