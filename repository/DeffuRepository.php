<?php
declare(strict_types=1);

namespace iutnc\deefy\repository;

use \PDO;
use \PDOException;

/**
 * La classe DeefyRepository gère la connexion à la base de données
 * en utilisant le design pattern Singleton.
 */
class DeefyRepository {

    /**
     * @var array $config Stocke les paramètres de connexion chargés depuis le fichier .ini.
     */
    private static array $config;

    /**
     * @var ?DeefyRepository $instance L'instance unique de la classe.
     */
    private static ?DeefyRepository $instance = null;

    /**
     * @var PDO $db L'objet de connexion PDO.
     */
    private PDO $db;

    /**
     * Le constructeur est privé pour empêcher l'instanciation directe.
     */
    private function __construct() {
        try {
            $this->db = new PDO(
                self::$config['driver'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['name'],
                self::$config['user'],
                self::$config['pass']
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    /**
     * Charge la configuration depuis un fichier .ini. [cite: 11]
     * @param string $file Le chemin vers le fichier de configuration.
     */
    public static function setConfig(string $file): void {
        self::$config = parse_ini_file($file);
    }
    
    /**
     * Retourne l'instance unique du repository. Si elle n'existe pas, elle est créée. [cite: 12]
     * @return DeefyRepository L'unique instance de la classe.
     */
    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
    }
}