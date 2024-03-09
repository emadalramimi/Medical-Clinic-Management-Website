<?php

namespace App\Model;

use DateTimeImmutable;
use App\Entity\Entity;
use App\Entity\Consultation;
use App\Entity\Usager;
use App\Entity\Medecin;

/**
 * Modèle des consultations
 */
class ConsultationsModel implements EntityModel {
    
    /**
     * Retourne toutes les consultations triées par date décroissante
     * @return array Les consultations triées par date décroissante
     */
    public function getAll(): array {
        $query = DBManager::getPDO()->query('
            SELECT * FROM consultations
            ORDER BY date_heure DESC
        ');
        $query->execute();

        $consultationsList = $query->fetchAll(\PDO::FETCH_ASSOC);
        $consultations = [];

        // Conversion des données en objets de type Consultation
        foreach($consultationsList as $consultation) {
            $consultations[] = self::toEntity($consultation);
        }

        return $consultations;
    }

    /**
     * Retourne une consultation à partir de ses identifiants
     * @param array $primaryKeys Les identifiants de la consultation (ex : ['id_usager' => 1, 'date_heure' => '2021-01-01 00:00:00'])
     * @return Consultation La consultation correspondant aux identifiants
     * @throws \InvalidArgumentException Si la consultation n'existe pas
     * @throws \PDOException Si la requête échoue
     */
    public function getById(array $primaryKeys): Consultation {
        $query = DBManager::getPDO()->prepare('SELECT * FROM consultations WHERE id_usager = :id_usager AND date_heure = :date_heure');
        $query->execute([
            'id_usager' => $primaryKeys['id_usager'],
            'date_heure' => $primaryKeys['date_heure']
        ]);

        if (!$query->rowCount()) {
            throw new \InvalidArgumentException('Cette consultation n\'existe pas');
        }

        // Retour de la conversion des données en objet de type Consultation
        return self::toEntity($query->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Ajoute une consultation
     * @param Consultation $consultation La consultation à ajouter
     * @throws \InvalidArgumentException Si la consultation existe déjà ou si le médecin ou l'usager sont déjà en consultation sur ce créneau
     * @throws \PDOException Si la requête échoue
     */
    public function add(Entity $consultation): void {
        if(!$consultation instanceof Consultation) {
            throw new \InvalidArgumentException('Cette méthode n\'accepte que des objets de type Consultation');
        }
        
        if($this->_checkOverlapMedecin($consultation)) {
            throw new \InvalidArgumentException('Ce médecin est déjà en consultation sur ce créneau');
        }

        if($this->_checkOverlapUsager($consultation)) {
            throw new \InvalidArgumentException('Cet usager est déjà en consultation sur ce créneau');
        }

        // Vérification que la consultation n'existe pas déjà
        $query = DBManager::getPDO()->prepare('
            SELECT * FROM consultations
            WHERE id_usager = :id_usager
            AND date_heure = :date_heure
        ');
        $query->execute([
            'id_usager' => $consultation->getUsager()->getIdUsager(),
            'date_heure' => $consultation->getDateHeure()->format('Y-m-d H:i')
        ]);

        if($query->rowCount()) {
            throw new \InvalidArgumentException('Cette consultation existe déjà');
        }

        // Ajout de la consultation
        $query = DBManager::getPDO()->prepare(
            'INSERT INTO consultations (
                id_usager,
                date_heure,
                duree,
                id_medecin
            ) VALUES (
                :id_usager,
                :date_heure,
                :duree,
                :id_medecin
            )'
        );
        $query->execute([
            'id_usager' => $consultation->getUsager()->getIdUsager(),
            'date_heure' => $consultation->getDateHeure()->format('Y-m-d H:i'),
            'duree' => $consultation->getDuree(),
            'id_medecin' => $consultation->getMedecin()->getIdMedecin()
        ]);
    }

    /**
     * Modifie une consultation
     * @param Consultation $consultation La consultation à modifier
     * @throws \InvalidArgumentException Si la consultation n'existe pas ou si le médecin ou l'usager sont déjà en consultation sur ce créneau
     * @throws \PDOException Si la requête échoue
     */
    public function edit(Entity $consultation): void {
        if(!$consultation instanceof Consultation) {
            throw new \InvalidArgumentException('Cette méthode n\'accepte que des objets de type Consultation');
        }

        if($this->_checkOverlapMedecin($consultation)) {
            throw new \InvalidArgumentException('Ce médecin est déjà en consultation sur ce créneau');
        }

        if($this->_checkOverlapUsager($consultation)) {
            throw new \InvalidArgumentException('Cet usager est déjà en consultation sur ce créneau');
        }

        $query = DBManager::getPDO()->prepare('
            UPDATE consultations
            SET id_usager = :id_usager,
                date_heure = :date_heure,
                duree = :duree,
                id_medecin = :id_medecin
            WHERE id_usager = :id_usager_old AND date_heure = :date_heure_old
        ');
        $query->execute([
            'id_usager' => $consultation->getUsager()->getIdUsager(),
            'date_heure' => $consultation->getDateHeure()->format('Y-m-d H:i'),
            'duree' => $consultation->getDuree(),
            'id_medecin' => $consultation->getMedecin()->getIdMedecin(),
            'id_usager_old' => $consultation->getIdUsagerOld(),
            'date_heure_old' => $consultation->getDateHeureOld()->format('Y-m-d H:i')
        ]);
    }

    /**
     * Supprime une consultation
     * @param array $primaryKeys Les identifiants de la consultation à supprimer (ex : ['id_usager' => 1, 'date_heure' => '2021-01-01 00:00:00'])
     * @throws \InvalidArgumentException Si la consultation n'existe pas
     * @throws \PDOException Si la requête échoue
     */
    public function delete(array $primaryKeys): void {
        $query = DBManager::getPDO()->prepare('DELETE FROM consultations WHERE id_usager = :id_usager AND date_heure = :date_heure');
        $query->execute([
            'id_usager' => $primaryKeys['id_usager'],
            'date_heure' => $primaryKeys['date_heure']
        ]);
    }

    /**
     * Retourne toutes les consultations d'un médecin et d'un créneau donné (passés, du jour ou futurs) triées par date décroissante
     * @param string $idMedecin L'identifiant du médecin (vide pour tous les médecins)
     * @param string $creneaux Le créneau des consultations à retourner (vides, "passes", "jour" ou "futurs")
     * @return array Les consultations filtrées et triées par date décroissante
     * @throws \InvalidArgumentException Si l'identifiant du médecin n'est pas null et n'est pas un nombre 
     * @throws \InvalidArgumentException Si le créneau n'est pas vide, "passes", "jour" ou "futurs"
     * @throws \PDOException Si la requête échoue
     */
    public function getByFiltrage(string $idMedecin, string $creneaux): array {
        if($idMedecin != '' && !is_numeric($idMedecin)) {
            throw new \InvalidArgumentException('L\'identifiant du médecin doit être un nombre');
        }
        if(!in_array($creneaux, ['', 'passes', 'jour', 'futurs'])) {
            throw new \InvalidArgumentException('Créneaux doit être vide, "passes", "jour" ou "futurs"');
        }
    
        // Si l'identifiant du médecin et creneaux ne sont pas fournis, retourne toutes les consultations
        if($idMedecin == '' && $creneaux == '') {
            return $this->getAll();
        }
    
        // Préparation de la requête
        $sql = 'SELECT * FROM consultations';
        $params = [];
    
        // Si l'identifiant du médecin est fourni, on filtre sur ce médecin
        if ($idMedecin != '') {
            $sql .= ' WHERE id_medecin = :id_medecin';
            $params['id_medecin'] = $idMedecin;
        }
    
        // Si le créneau est fourni, on filtre sur ce créneau
        if ($creneaux != '') {
            // Si l'identifiant du médecin est fourni, on ajoute un AND, sinon un WHERE
            $sql .= $idMedecin != '' ? ' AND' : ' WHERE';
    
            // Filtre sur le créneau demandé
            switch ($creneaux) {
                case 'passes':
                    $sql .= ' date_heure < NOW()';
                    break;
                case 'jour':
                    $sql .= ' DATE(date_heure) = CURDATE()';
                    break;
                case 'futurs':
                    $sql .= ' date_heure > NOW()';
                    break;
            }
        }
    
        // Tri par date décroissante
        $sql .= ' ORDER BY date_heure DESC';
    
        $query = DBManager::getPDO()->prepare($sql);
        $query->execute($params);
    
        $consultationsList = $query->fetchAll(\PDO::FETCH_ASSOC);
        $consultations = [];
    
        // Conversion des données en objets de type Consultation
        foreach($consultationsList as $consultation) {
            $consultations[] = self::toEntity($consultation);
        }
    
        return $consultations;
    }

    /**
     * Convertit le tableau data en objet Consultation
     * @param array $data Tableau contenant les données à convertir
     */
    public static function toEntity(array $data): Entity {
        $usagersModel = new UsagersModel();
        $medecinsModel = new MedecinsModel();
        return new Consultation(
            $data['id_usager'] != '' ? $usagersModel->getById(['id_usager' => $data['id_usager']]) : null,
            $data['date_heure'] != '' ? new DateTimeImmutable($data['date_heure']) : null,
            $data['duree'] ?: null,
            $data['id_medecin'] != '' ? $medecinsModel->getById(['id_medecin' => $data['id_medecin']]) : null,
            isset($data['id_usager_old']) && $data['id_usager_old'] != '' ? $data['id_usager_old'] : null,
            isset($data['date_heure_old']) && $data['date_heure_old'] != '' ? new DateTimeImmutable($data['date_heure_old']) : null,
        );
    }

    /**
     * Vérifie si le médecin est déjà en consultation sur ce créneau
     * @param Consultation $consultation La consultation à vérifier
     * @return bool True si le médecin est déjà en consultation sur ce créneau, false sinon
     * @throws \PDOException Si la requête échoue
     */
    private function _checkOverlapMedecin(Consultation $consultation): bool {
        $query = DBManager::getPDO()->prepare('
            SELECT * FROM consultations
            WHERE id_usager != :id_usager
            AND date_heure != :date_heure
            AND id_medecin = :id_medecin
            AND ((:date_heure BETWEEN date_heure
                    AND DATE_ADD(date_heure, INTERVAL duree MINUTE))
                    OR
                    (DATE_ADD(:date_heure, INTERVAL :duree MINUTE) BETWEEN date_heure
                    AND DATE_ADD(date_heure, INTERVAL duree MINUTE)))
        ');
        $query->execute([
            'id_usager' => $consultation->getUsager()->getIdUsager(),
            'id_medecin' => $consultation->getMedecin()->getIdMedecin(),
            'date_heure' => $consultation->getDateHeure()->format('Y-m-d H:i'),
            'duree' => $consultation->getDuree()
        ]);
        return $query->rowCount() > 0;
    }

    /**
     * Vérifie si l'usager est déjà en consultation sur ce créneau
     * @param Consultation $consultation La consultation à vérifier
     * @return bool True si l'usager est déjà en consultation sur ce créneau, false sinon
     * @throws \PDOException Si la requête échoue
     */
    private function _checkOverlapUsager(Consultation $consultation): bool {
        $query = DBManager::getPDO()->prepare('
            SELECT * FROM consultations
            WHERE id_medecin != :id_medecin
            AND date_heure != :date_heure
            AND id_usager = :id_usager
            AND ((:date_heure BETWEEN date_heure
                    AND DATE_ADD(date_heure, INTERVAL duree MINUTE))
                    OR
                    (DATE_ADD(:date_heure, INTERVAL :duree MINUTE) BETWEEN date_heure
                    AND DATE_ADD(date_heure, INTERVAL duree MINUTE)))
        ');
        $query->execute([
            'id_usager' => $consultation->getUsager()->getIdUsager(),
            'id_medecin' => $consultation->getMedecin()->getIdMedecin(),
            'date_heure' => $consultation->getDateHeure()->format('Y-m-d H:i'),
            'duree' => $consultation->getDuree()
        ]);
        return $query->rowCount() > 0;
    }

}