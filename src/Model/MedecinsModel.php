<?php

namespace App\Model;

use App\Entity\Entity;
use App\Entity\Medecin;
use App\Model\ConsultationsModel;

/**
 * Modèle des médecins
 */
class MedecinsModel implements EntityModel {
    
    /**
     * Méthode de récupération de tous les médecins
     * @return Medecin[] Liste des médecins
     * @throws \PDOException Erreur SQL
     */
    public function getAll(): array {
        $query = DBManager::getPDO()->query('
            SELECT * FROM medecins
            ORDER BY nom, prenom
        ');
        $query->execute();

        $medecinsList = $query->fetchAll(\PDO::FETCH_ASSOC);
        $medecins = [];
        
        // Pour chaque médecin, on crée un objet Medecin et on l'ajoute dans le tableau
        foreach($medecinsList as $medecin) {
            $medecins[] = self::toEntity($medecin);
        }

        return $medecins;
    }

    /**
     * Méthode de récupération des médecins par filtrage
     * @param string $recherche Chaîne de caractères à rechercher dans le nom et le prénom
     * @return Medecin[] Liste des médecins
     * @throws \PDOException Erreur SQL
     */
    public function getByFiltrage(string $recherche): array {
        // Construction de la requête SQL de base
        $queryText = 'SELECT * FROM medecins ';
        
        // Initialisation des paramètres de la requête
        $parameters = [];

        // Vérification si la recherche n'est pas vide
        if (!empty($recherche)) {
            // Séparation des termes de recherche
            $searchTerms = explode(' ', $recherche);
            $queryText .= 'WHERE ';

            // Boucle à travers chaque terme de recherche
            foreach ($searchTerms as $index => $term) {
                // Ajout de l'opérateur logique AND si ce n'est pas le premier terme
                if ($index > 0) {
                    $queryText .= 'AND ';
                }

                // Construction de la condition de recherche sur le nom ou prénom
                $queryText .= '(nom LIKE :recherche' . $index . ' OR prenom LIKE :recherche' . $index . ') ';
                $parameters['recherche' . $index] = '%' . $term . '%';
            }
        }
    
        // Ajout de l'ordre de tri par nom, puis prénom
        $queryText .= 'ORDER BY nom, prenom';
    
        $query = DBManager::getPDO()->prepare($queryText);
        $query->execute($parameters);
    
        $medecinsList = $query->fetchAll(\PDO::FETCH_ASSOC);
        $medecins = [];
    
        // Pour chaque médecin, on crée un objet Medecin et on l'ajoute dans le tableau
        foreach ($medecinsList as $medecin) {
            $medecins[] = self::toEntity($medecin);
        }
    
        return $medecins;
    }

    /**
     * Méthode de récupération d'un médecin par son identifiant
     * @param array $primaryKeys Tableau contenant l'identifiant du médecin
     * @return Medecin Médecin correspondant à l'identifiant
     * @throws \InvalidArgumentException Si le médecin n'existe pas
     * @throws \PDOException Erreur SQL
     */
    public function getById(array $primaryKeys): Medecin {
        $query = DBManager::getPDO()->prepare('SELECT * FROM medecins WHERE id_medecin = :id_medecin');
        $query->execute([
            'id_medecin' => $primaryKeys['id_medecin']
        ]);

        if (!$query->rowCount()) {
            throw new \InvalidArgumentException('Ce médecin n\'existe pas');
        }

        // Retour du résultat de la requête sous forme d'objet Medecin
        return self::toEntity($query->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Méthode d'ajout d'un médecin
     * @param Medecin $medecin Médecin à ajouter
     * @throws \InvalidArgumentException Si l'objet fourni n'est pas de type Medecin
     * @throws \PDOException Erreur SQL
     */
    public function add(Entity $medecin): void {
        if(!$medecin instanceof Medecin) {
            throw new \InvalidArgumentException('Cette méthode n\'accepte que des objets de type Medecin');
        }

        $query = DBManager::getPDO()->prepare(
            'INSERT INTO medecins (
                civilite,
                nom,
                prenom
            ) VALUES (
                :civilite,
                :nom,
                :prenom
            )'
        );
        $query->execute([
            'civilite' => $medecin->getCivilite(),
            'nom' => $medecin->getNom(),
            'prenom' => $medecin->getPrenom()
        ]);
    }

    /**
     * Méthode de modification d'un médecin
     * @param Medecin $medecin Médecin à modifier
     * @throws \InvalidArgumentException Si l'objet fourni n'est pas de type Medecin
     * @throws \PDOException Erreur SQL
     */
    public function edit(Entity $medecin): void {
        if(!$medecin instanceof Medecin) {
            throw new \InvalidArgumentException('Cette méthode n\'accepte que des objets de type Medecin');
        }

        $query = DBManager::getPDO()->prepare('
            UPDATE medecins
            SET civilite = :civilite,
                nom = :nom,
                prenom = :prenom
            WHERE id_medecin = :id_medecin
        ');
        $query->execute([
            'civilite' => $medecin->getCivilite(),
            'nom' => $medecin->getNom(),
            'prenom' => $medecin->getPrenom(),
            'id_medecin' => $medecin->getIdMedecin()
        ]);
    }

    /**
     * Méthode de suppression d'un médecin
     * @param array $primaryKeys Tableau contenant l'identifiant du médecin
     * @throws \InvalidArgumentException Si le médecin n'existe pas ou est référencé par une consultation
     * @throws \PDOException Erreur SQL
     */
    public function delete(array $primaryKeys): void {
        // Vérification que le médecin existe et n'est pas référencé par une consultation
        $medecin = $this->getById($primaryKeys);
        if(!$medecin) {
            throw new \InvalidArgumentException('Ce médecin n\'existe pas');
        }

        // Mise à jour des usagers ayant ce médecin traitant
        $query = DBManager::getPDO()->prepare('UPDATE usagers SET id_medecin = NULL where id_medecin = :id_medecin');
        $query->execute([
            'id_medecin' => $primaryKeys['id_medecin']
        ]);

        // Suppression des consultations du médecin
        $query = DBManager::getPDO()->prepare('DELETE FROM consultations WHERE id_medecin = :id_medecin');
        $query->execute([
            'id_medecin' => $primaryKeys['id_medecin']
        ]);

        // Suppression du médecin
        $query = DBManager::getPDO()->prepare('DELETE FROM medecins WHERE id_medecin = :id_medecin');
        $query->execute([
            'id_medecin' => $primaryKeys['id_medecin']
        ]);
    }

    /**
     * Convertit le tableau data en objet Medecin
     * @param array $data Tableau contenant les données à convertir
     */
    public static function toEntity(array $data): Entity {
        return new Medecin(
            $data['id_medecin'] ?? null,
            $data['civilite'] ?? null,
            $data['nom'] ?? null,
            $data['prenom'] ?? null
        );
    }

}