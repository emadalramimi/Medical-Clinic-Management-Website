<?php

namespace App\Model;

use DateTimeImmutable;
use App\Entity\Entity;
use App\Entity\Usager;

/**
 * Modèle des usagers
 */
class UsagersModel implements EntityModel {

    /**
     * Retourne la liste de tous les usagers triés par nom et prénom
     * @return array Liste des usagers triés par nom et prénom
     */
    public function getAll(): array {
        $query = DBManager::getPDO()->query('
            SELECT * FROM usagers
            ORDER BY nom, prenom
        ');
        $query->execute();

        $usagersList = $query->fetchAll(\PDO::FETCH_ASSOC);
        $usagers = [];

        // Pour chaque usager, on crée un objet Usager et on l'ajoute dans le tableau
        foreach($usagersList as $usager) {
            $usagers[] = self::toEntity($usager);
        }

        return $usagers;
    }

    /**
     * Retourne la liste des usagers par filtrage
     * @param string $recherche Chaîne de caractères à rechercher dans le nom et le prénom
     * @param string $idMedecin Identifiant du médecin pour filtrer les usagers
     * @return array Liste des usagers par filtrage
     */
    public function getByFiltrage(string $recherche, string $idMedecin): array {
        $queryText = 'SELECT * FROM usagers ';
        $parameters = [];
        
        // Vérifie s'il y a une recherche par mots-clés
        if (!empty($recherche)) {
            $searchTerms = explode(' ', $recherche);
            $queryText .= 'WHERE ';
    
            foreach ($searchTerms as $index => $term) {
                // Ajoute les conditions de recherche pour chaque terme
                if ($index > 0) {
                    $queryText .= 'AND ';
                }
    
                // Condition de recherche sur le nom ou prénom
                $queryText .= '(nom LIKE :recherche' . $index . ' OR prenom LIKE :recherche' . $index . ') ';
                $parameters['recherche' . $index] = '%' . $term . '%';
            }
    
            // Vérifie s'il y a un filtre par id de médecin
            if (!empty($idMedecin)) {
                // Ajoute une condition supplémentaire pour l'id du médecin
                $queryText .= 'AND id_medecin = :id_medecin ';
                $parameters['id_medecin'] = $idMedecin;
            }
        } else if (!empty($idMedecin)) {
            // Si aucun terme de recherche n'est spécifié, applique uniquement le filtre par id de médecin
            $queryText .= 'WHERE id_medecin = :id_medecin ';
            $parameters = ['id_medecin' => $idMedecin];
        }
    
        // Ajout de l'ordre de tri par nom, puis prénom
        $queryText .= 'ORDER BY nom, prenom';
    
        $query = DBManager::getPDO()->prepare($queryText);
        $query->execute($parameters);
    
        $usagersList = $query->fetchAll(\PDO::FETCH_ASSOC);
        $usagers = [];
    
        // Pour chaque usager, on crée un objet Usager et on l'ajoute dans le tableau
        foreach ($usagersList as $usager) {
            $usagers[] = self::toEntity($usager);
        }
    
        return $usagers;
    }

    /**
     * Retourne l'usager correspondant à l'identifiant fourni
     * @param array $primaryKeys Tableau contenant l'identifiant de l'usager à récupérer (ex : ['id_usager' => 1])
     * @return Usager L'usager correspondant à l'identifiant fourni
     * @throws \InvalidArgumentException Si l'usager n'existe pas
     */
    public function getById(array $primaryKeys): Usager {
        $query = DBManager::getPDO()->prepare('SELECT * FROM usagers WHERE id_usager = :id_usager');
        $query->execute([
            'id_usager' => $primaryKeys['id_usager']
        ]);

        if (!$query->rowCount()) {
            throw new \InvalidArgumentException('Cet usager n\'existe pas');
        }

        // Retour du résultat de la requête sous forme d'objet Usager
        return self::toEntity($query->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Ajoute un usager
     * @param Usager $usager Usager à ajouter
     * @throws \InvalidArgumentException Si l'objet fourni n'est pas de type Usager
     * @throws \InvalidArgumentException Si le numéro de sécurité sociale est déjà utilisé
     * @throws \InvalidArgumentException Si le médecin n'existe pas
     * @throws \PDOException Erreur SQL
     */
    public function add(Entity $usager): void {
        if(!$usager instanceof Usager) {
            throw new \InvalidArgumentException('Cette méthode n\'accepte que des objets de type Usager');
        }

        // Vérification que le médecin existe
        $this->_verifierMedecin($usager);
        
        // Vérification que le numéro de sécurité sociale n'est pas déjà utilisé
        $query = DBManager::getPDO()->prepare('
            SELECT * FROM usagers
            WHERE num_securite_sociale = :num_securite_sociale
        ');
        $query->execute([
            'num_securite_sociale' => $usager->getNumSecuriteSociale()
        ]);
        if($query->rowCount()) {
            throw new \InvalidArgumentException('Ce numéro de sécurité sociale est déjà utilisé');
        }

        // Ajout de l'usager
        $query = DBManager::getPDO()->prepare(
            'INSERT INTO usagers (
                civilite,
                nom,
                prenom,
                adresse,
                ville,
                code_postal,
                date_naissance,
                ville_naissance,
                code_postal_naissance,
                num_securite_sociale,
                id_medecin
            ) VALUES (
                :civilite,
                :nom,
                :prenom,
                :adresse,
                :ville,
                :code_postal,
                :date_naissance,
                :ville_naissance,
                :code_postal_naissance,
                :num_securite_sociale,
                :id_medecin
            )'
        );
        $query->execute([
            'civilite' => $usager->getCivilite(),
            'nom' => $usager->getNom(),
            'prenom' => $usager->getPrenom(),
            'adresse' => $usager->getAdresse(),
            'ville' => $usager->getVille(),
            'code_postal' => $usager->getCodePostal(),
            'date_naissance' => $usager->getDateNaissance()->format('Y-m-d'),
            'ville_naissance' => $usager->getVilleNaissance(),
            'code_postal_naissance' => $usager->getCodePostalNaissance(),
            'num_securite_sociale' => $usager->getNumSecuriteSociale(),
            'id_medecin' => $usager->getMedecin() ? $usager->getMedecin()->getIdMedecin() : null
        ]);
    }

    /**
     * Méthode de modification d'un usager
     * @param Usager $usager Usager à modifier
     * @throws \InvalidArgumentException Si l'objet fourni n'est pas de type Usager
     * @throws \InvalidArgumentException Si le numéro de sécurité sociale est déjà utilisé
     * @throws \InvalidArgumentException Si le médecin n'existe pas
     * @throws \PDOException Erreur SQL
     */
    public function edit(Entity $usager): void {
        if(!$usager instanceof Usager) {
            throw new \InvalidArgumentException('Cette méthode n\'accepte que des objets de type Usager');
        }

        // Vérification que le médecin existe
        $this->_verifierMedecin($usager);
        
        // Vérification que le numéro de sécurité sociale n'est pas déjà utilisé
        $query = DBManager::getPDO()->prepare('
            SELECT * FROM usagers
            WHERE num_securite_sociale = :num_securite_sociale
            AND id_usager != :id_usager
        ');
        $query->execute([
            'num_securite_sociale' => $usager->getNumSecuriteSociale(),
            'id_usager' => $usager->getIdUsager()
        ]);
        if($query->rowCount()) {
            throw new \InvalidArgumentException('Ce numéro de sécurité sociale est déjà utilisé');
        }

        // Modification de l'usager
        $query = DBManager::getPDO()->prepare('
            UPDATE usagers
            SET civilite = :civilite,
                nom = :nom,
                prenom = :prenom,
                adresse = :adresse,
                ville = :ville,
                code_postal = :code_postal,
                date_naissance = :date_naissance,
                ville_naissance = :ville_naissance,
                code_postal_naissance = :code_postal_naissance,
                num_securite_sociale = :num_securite_sociale,
                id_medecin = :id_medecin
            WHERE id_usager = :id_usager
        ');
        $query->execute([
            'civilite' => $usager->getCivilite(),
            'nom' => $usager->getNom(),
            'prenom' => $usager->getPrenom(),
            'adresse' => $usager->getAdresse(),
            'ville' => $usager->getVille(),
            'code_postal' => $usager->getCodePostal(),
            'date_naissance' => $usager->getDateNaissance()->format('Y-m-d'),
            'ville_naissance' => $usager->getVilleNaissance(),
            'code_postal_naissance' => $usager->getCodePostalNaissance(),
            'num_securite_sociale' => $usager->getNumSecuriteSociale(),
            'id_medecin' => $usager->getMedecin() ? $usager->getMedecin()->getIdMedecin() : null,
            'id_usager' => $usager->getIdUsager()
        ]);
    }

    /**
     * Méthode de suppression d'un usager
     * @param array $primaryKeys Tableau contenant l'identifiant de l'usager
     * @throws \InvalidArgumentException Si l'usager n'existe pas
     * @throws \InvalidArgumentException Si l'usager est référencé par une consultation
     * @throws \PDOException Erreur SQL
     */
    public function delete(array $primaryKeys): void {
        // Vérification que l'usager existe et n'est pas référencé par une consultation
        $usager = $this->getById($primaryKeys);
        if(!$usager) {
            throw new \InvalidArgumentException('Cet usager n\'existe pas');
        }
        
        // Suppression des consultations de l'usager
        $query = DBManager::getPDO()->prepare('DELETE FROM consultations WHERE id_usager = :id_usager');
        $query->execute([
            'id_usager' => $primaryKeys['id_usager']
        ]);

        // Suppression de l'usager
        $query = DBManager::getPDO()->prepare('DELETE FROM usagers WHERE id_usager = :id_usager');
        $query->execute([
            'id_usager' => $primaryKeys['id_usager']
        ]);
    }

    /**
     * Convertit le tableau data en objet Usager
     * @param array $data Tableau contenant les données à convertir
     */
    public static function toEntity(array $data): Entity {
        $medecinsModel = new MedecinsModel;
        return new Usager(
            isset($data['id_usager']) && $data['id_usager'] != '' ? intval($data['id_usager']) : null,
            $data['civilite'] ?? null,
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['adresse'] ?? null,
            $data['ville'] ?? null,
            $data['code_postal'] ?? null,
            $data['date_naissance'] != '' ? new DateTimeImmutable($data['date_naissance']) : null,
            $data['ville_naissance'] ?? null,
            $data['code_postal_naissance'] ?? null,
            $data['num_securite_sociale'] ?? null,
            $data['id_medecin'] != '' ? $medecinsModel->getById(['id_medecin' => $data['id_medecin']]) : null
        );
    }

    /**
     * Vérifie que le médecin traitant de l'usager existe
     * @param Usager $usager Usager à vérifier
     * @throws \InvalidArgumentException Si le médecin n'existe pas
     */
    private function _verifierMedecin(Usager $usager): void {
        if($usager->getMedecin()) {
            $medecinsModel = new MedecinsModel();
            $medecin = $medecinsModel->getById(['id_medecin' => $usager->getMedecin()->getIdMedecin()]);

            if(!$medecin) {
                throw new \InvalidArgumentException('Ce médecin n\'existe pas');
            }
        }
    }

}