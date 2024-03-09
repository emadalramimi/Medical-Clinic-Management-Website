<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Entity\Entity;
use App\Model\ListedExceptions;

/**
 * Class métier Consultation
 */
class Consultation implements Entity {

    private Usager $usager;
    private DateTimeImmutable $dateHeure;
    private int $duree;
    private Medecin $medecin;
    private int $idUsagerOld;
    private DateTimeImmutable $dateHeureOld;

    /**
     * Constructeur de la classe Consultation
     * @param Usager $usager L'usager de la consultation
     * @param DateTimeImmutable $dateHeure La date et l'heure de la consultation
     * @param int $duree La durée de la consultation en minutes (120 min maximum)
     * @param Medecin $medecin Le médecin de la consultation
     * @throws ListedExceptions Si un des paramètres est invalide
     */
    public function __construct(Usager $usager, DateTimeImmutable $dateHeure, int $duree, Medecin $medecin, ?int $idUsagerOld, ?DateTimeImmutable $dateHeureOld) {
        $exceptions = new ListedExceptions();

        if($usager == null || $dateHeure == null || $duree == null || $medecin == null) {
            $exceptions->addException(new \InvalidArgumentException('Tous les champs doivent être remplis'));
        }
        if($duree <= 0) {
            $exceptions->addException(new \InvalidArgumentException('La durée de la consultation doit être supérieure à 0'));
        }
        if($duree > 120) {
            $exceptions->addException(new \InvalidArgumentException('La durée de la consultation ne peut pas dépasser 2 heures (120 min)'));
        }

        if (!$exceptions->isEmpty()) {
            throw $exceptions;
        }

        $this->usager = $usager;
        $this->dateHeure = $dateHeure;
        $this->duree = $duree;
        $this->medecin = $medecin;
        $this->idUsagerOld = $idUsagerOld ?? $usager->getIdUsager();
        $this->dateHeureOld = $dateHeureOld ?? $dateHeure;
    }

    /**
     * Retourne l'usager de la consultation
     * @return Usager L'usager de la consultation
     */
    public function getUsager(): Usager {
        return $this->usager;
    }

    /**
     * Modifie l'usager de la consultation
     * @param Usager $usager L'usager de la consultation
     * @throws \InvalidArgumentException Si l'usager est null
     */
    public function setUsager(Usager $usager): void {
        if($usager == null) {
            throw new \InvalidArgumentException('usager ne peut être null');
        }

        $this->usager = $usager;
    }

    /**
     * Retourne la date et l'heure de la consultation
     * @return DateTimeImmutable La date et l'heure de la consultation
     */
    public function getDateHeure(): DateTimeImmutable {
        return $this->dateHeure;
    }

    /**
     * Modifie la date et l'heure de la consultation
     * @param DateTimeImmutable $dateHeure La date et l'heure de la consultation
     * @throws \InvalidArgumentException Si la date et l'heure sont null
     */
    public function setDateHeure(DateTimeImmutable $dateHeure): void {
        if($dateHeure == null) {
            throw new \InvalidArgumentException('dateHeure ne peut être null');
        }
        $this->dateHeure = $dateHeure;
    }

    /**
     * Retourne la durée de la consultation en minutes
     * @return int La durée de la consultation en minutes
     */
    public function getDuree(): int {
        return $this->duree;
    }

    /**
     * Modifie la durée de la consultation en minutes
     * @param int $duree La durée de la consultation en minutes
     * @throws \InvalidArgumentException Si la durée est null, inférieure à 0 ou supérieure à 120
     */
    public function setDuree($duree): void {
        if($duree == null) {
            throw new \InvalidArgumentException('duree ne peut être null');
        }
        if($duree < 0) {
            throw new \InvalidArgumentException('duree doit être supérieur à 0');
        }
        if($duree > 120) {
            throw new \InvalidArgumentException('duree ne peut pas dépasser 120');
        }

        $this->duree = $duree;
    }

    /**
     * Retourne le médecin de la consultation
     * @return Medecin Le médecin de la consultation
     */
    public function getMedecin(): Medecin {
        return $this->medecin;
    }

    /**
     * Modifie le médecin de la consultation
     * @param Medecin $medecin Le médecin de la consultation
     * @throws \InvalidArgumentException Si le médecin est null
     */
    public function setMedecin($medecin): void {
        if($medecin == null) {
            throw new \InvalidArgumentException('medecin ne peut être null');
        }

        $this->medecin = $medecin;
    }

    /**
     * Retourne l'id de l'usager de la consultation pour la modification
     * @return int L'id de l'usager de la consultation pour la modification
     */
    public function getIdUsagerOld(): int {
        return $this->idUsagerOld;
    }

    /**
     * Retourne la date et l'heure de la consultation pour la modification
     * @return DateTimeImmutable La date et l'heure de la consultation pour la modification
     */
    public function getDateHeureOld(): DateTimeImmutable {
        return $this->dateHeureOld;
    }

}