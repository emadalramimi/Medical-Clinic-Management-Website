<?php

namespace App\Model;

use App\Entity\Entity;

/**
 * Interface EntityModel
 * Interface de base pour les modèles d'entités (classes représentant les tables de la base de données)
 */
interface EntityModel {

    public function getAll(): array;

    public function getById(array $primaryKeys): Entity;

    public function add(Entity $entity): void;

    public function edit(Entity $entity): void;

    public function delete(array $primaryKeys): void;

    public static function toEntity(array $data): Entity;

}