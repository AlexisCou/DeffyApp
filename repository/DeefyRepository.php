<?php
declare(strict_types=1);

namespace iutnc\deefy\repository;

use \PDO;
use \PDOException;

class DeefyRepository {

 
    private static array $config;
    private static ?DeefyRepository $instance = null;
    private PDO $db;


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

    public static function setConfig(string $file): void {
        self::$config = parse_ini_file($file);
    }

    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
    }
}