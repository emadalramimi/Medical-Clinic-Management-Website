<?php

use App\Controller\SecurityController;
use App\Controller\NightModeController;

$securityController = new SecurityController();
$nightModeController = new NightModeController();

?>

<header class="header">
    <div class="wrapper">
        <nav class="nav">
            <span class="nav-item mobile">Menu v</span>
            <ul>
                <li class="nav-item"><a href="./consultations.php"><i class="bi bi-calendar-fill"></i>Consultations</a></li>
                <li class="nav-item"><a href="./usagers.php"><i class="bi bi-people-fill"></i>Usagers</a></li>
                <li class="nav-item"><a href="./medecins.php"><i class="bi bi-heart-pulse-fill"></i>Médecins</a></li>
                <li class="nav-item"><a href="./statistiques.php"><i class="bi bi-bar-chart-line-fill"></i>Statistiques</a></li>
                <li class="nav-item"><a href="./toggle-night-mode.php"><?= !$nightModeController->isNightMode() ? '<i class="bi bi-moon-stars-fill"></i>Mode nuit' : '<i class="bi bi-sun-fill"></i>Mode jour' ?></a></li>
                <?php if ($securityController->isLogged()): ?><li class="nav-item"><a href="./deconnexion.php"><i class="bi bi-door-closed-fill"></i>Déconnexion</a></li><?php endif ?>
            </ul>
        </nav>
    </div>
</header>