<?php

namespace App\Model;

/**
 * Classe de gestion de la connexion à la base de données
 */
class DBManager {

    private static $pdo;

    /**
     * Méthode de connexion à la base de données (singleton)
     * @return \PDO Objet PDO de connexion à la base de données
     * @throws \PDOException Erreur de connexion à la base de données
     */
    public static function getPDO(): \PDO{
        if(!isset($pdo)){
            $dbConfig = self::_getDBConfig();
            try {
                self::$pdo = new \PDO($dbConfig['DB_TYPE'].':host='.$dbConfig['DB_HOST'].';dbname='.$dbConfig['DB_NAME'].';charset='.$dbConfig['DB_CHARSET'], $dbConfig['DB_USER'], $dbConfig['DB_PASSWORD']);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    /**
     * Méthode de récupération des variables de configuration de la base de données
     * @return array Variables de configuration de la base de données
     */
    private static function _getDBConfig(): array {
        $dbConfig = [];

        // Chemin du fichier .env
        $envFilePath = realpath(__DIR__ . '/../../.env');

        if (file_exists($envFilePath)) {
            $envContent = file_get_contents($envFilePath);

            // Sépare le contenu en lignes
            $envLines = explode("\n", $envContent);

            // Parcours chaque ligne pour extraire les variables
            foreach ($envLines as $line) {
                // Ignore les lignes vides ou celles ne contenant pas de "="
                if (empty($line) || strpos($line, '=') === false) {
                    continue;
                }

                // Sépare la clé et la valeur en fonction du "="
                list($key, $value) = explode('=', $line, 2);

                // Supprime les espaces et les guillemets autour de la valeur
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");

                // Définit la variable d'environnement si elle n'est pas déjà définie
                if (!array_key_exists($key, $_ENV)) {
                    $dbConfig[$key] = $value;
                }
            }
        }

        return $dbConfig;
    }

}