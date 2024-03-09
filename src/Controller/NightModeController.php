<?php

namespace App\Controller;

class NightModeController {

    public function toggleNightMode() {
        if (isset($_COOKIE['night-mode'])) {
            setcookie('night-mode', 'false', time() - 60 * 60 * 24 * 365);
        } else {
            setcookie('night-mode', 'true', time() + 60 * 60 * 24 * 365);
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function isNightMode() {
        return isset($_COOKIE['night-mode']) && $_COOKIE['night-mode'] === 'true';
    }

    public function getNightModeClass() {
        return $this->isNightMode() ? 'night' : '';
    }

}