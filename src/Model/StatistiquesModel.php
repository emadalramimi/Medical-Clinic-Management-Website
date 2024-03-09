<?php

namespace App\Model;

/**
 * Modèle de la page des statistiques
 */
class StatistiquesModel {

    /**
     * Retourne le nombre d'heures de consultation par médecin (seulement les consultations passées)
     * @param string|null $recherche La recherche à effectuer sur le nom et le prénom des médecins
     * @return array Le nombre d'heures de consultation par médecin (seulement les consultations passées)
     */
    public function getStatistiquesMedecins(?string $recherche): array {
        $queryText = '
            SELECT medecins.id_medecin, medecins.nom, medecins.prenom, SUM(consultations.duree) as nb_heures 
            FROM consultations, medecins
            WHERE consultations.id_medecin = medecins.id_medecin
            AND DATE_ADD(consultations.date_heure, INTERVAL consultations.duree MINUTE) < NOW()
        ';
    
        $parameters = [];
    
        if (!empty($recherche)) {
            // Séparation des termes de recherche
            $searchTerms = explode(' ', $recherche);
            $queryText .= ' AND '; // Use AND instead of WHERE
    
            // Boucle à travers chaque terme de recherche
            foreach ($searchTerms as $index => $term) {
                // Ajout de l'opérateur logique AND si ce n'est pas le premier terme
                if ($index > 0) {
                    $queryText .= ' AND ';
                }
    
                // Construction de la condition de recherche sur le nom ou prénom
                $queryText .= '(medecins.nom LIKE :recherche' . $index . ' OR medecins.prenom LIKE :recherche' . $index . ') ';
                $parameters['recherche' . $index] = '%' . $term . '%';
            }
        }
        
        $queryText .= ' GROUP BY medecins.id_medecin ORDER BY nb_heures DESC, medecins.nom, medecins.prenom';
    
        $query = DBManager::getPDO()->prepare($queryText);
        $query->execute($parameters);
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les statistiques de répartition des usagers
     * @return array Statistiques de répartition des usagers
     */
    public function getStatistiquesRepartitionUsagers(): array {
        $statistiques = [
            'hommes' => [
                'moins_25_ans' => $this->_compterUsagersParAgeEtSexe('M', 0, 25),
                'entre_25_et_50_ans' => $this->_compterUsagersParAgeEtSexe('M', 25, 50),
                'plus_50_ans' => $this->_compterUsagersParAgeEtSexe('M', 50),
            ],
            'femmes' => [
                'moins_25_ans' => $this->_compterUsagersParAgeEtSexe('Mme', 0, 25),
                'entre_25_et_50_ans' => $this->_compterUsagersParAgeEtSexe('Mme', 25, 50),
                'plus_50_ans' => $this->_compterUsagersParAgeEtSexe('Mme', 50),
            ],
        ];
    
        return $statistiques;
    }
    
    /**
     * Compte le nombre d'usagers par âge et par sexe
     * @param string $civilite La civilité des usagers à compter
     * @param int $lowerBound La borne inférieure de l'âge des usagers à compter
     * @param int|null $upperBound La borne supérieure de l'âge des usagers à compter
     * @return int Le nombre d'usagers par âge et par sexe
     * @throws \InvalidArgumentException Si la civilité n'est pas égale à "M" ou "Mme"
     */
    private function _compterUsagersParAgeEtSexe(string $civilite, int $lowerBound, ?int $upperBound = null): int {
        if($civilite !== 'M' && $civilite !== 'Mme') {
            throw new \InvalidArgumentException('La civilité doit être égale à "M" ou "Mme"');
        }
    
        // Construction de la requête SQL de base
        $query = 'SELECT COUNT(*) AS nb_usagers FROM usagers WHERE civilite = :civilite';
        $params = [':civilite' => $civilite];
    
        // Ajout des conditions sur l'age selon les bornes fournies
        if ($upperBound == null) {
            $query .= ' AND YEAR(CURRENT_DATE) - YEAR(date_naissance) >= :lowerBound';
            $params[':lowerBound'] = $lowerBound;
        } else {
            $query .= ' AND YEAR(CURRENT_DATE) - YEAR(date_naissance) >= :lowerBound AND YEAR(CURRENT_DATE) - YEAR(date_naissance) < :upperBound';
            $params[':lowerBound'] = $lowerBound;
            $params[':upperBound'] = $upperBound;
        }
    
        $stmt = DBManager::getPDO()->prepare($query);
        $stmt->execute($params);
        
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['nb_usagers'];
    }

}