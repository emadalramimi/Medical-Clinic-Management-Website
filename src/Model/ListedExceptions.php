<?php

namespace App\Model;

/**
 * Classe ListedExceptions
 * Classe permettant de gérer une liste d'exceptions
 */
class ListedExceptions extends \Exception {

    private array $exceptions;

    /**
     * Ajoute une exception à la liste, ou une liste d'exceptions
     * @param \Exception|ListedExceptions $exceptions Exception ou liste d'exceptions à ajouter
     * @return void
     */
    public function addException(\Exception $exceptions): void {
        if($exceptions instanceof ListedExceptions) {
            foreach ($exceptions->exceptions as $exception) {
                $this->exceptions[] = $exception;
            }
        } else {
            $this->exceptions[] = $exceptions;
        }
    }

    /**
     * Retourne true si la liste d'exceptions est vide, false sinon
     * @return bool True si la liste d'exceptions est vide, false sinon
     */
    public function isEmpty(): bool {
        return empty($this->exceptions);
    }

    /**
     * Retourne la liste d'exceptions sous forme de liste HTML
     * @return string La liste d'exceptions sous forme de liste HTML
     */
    public function formatExceptions(): string {
        $list = '<ul>';
        foreach ($this->exceptions as $exception) {
            $list .= '<li>' . $exception->getMessage() . '</li>';
        }
        $list .= '</ul>';
        
        return $list;
    }

}