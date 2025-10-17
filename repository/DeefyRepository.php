<?php
declare(strict_types=1);

namespace iutnc\deefy\repository;

use iutnc\deefy\classes\AudioTrack;
use iutnc\deefy\classes\Playlist;
use \PDO;
use \PDOException;

class DeefyRepository {

    private PDO $db;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct() {
        try {
            $this->db = new PDO(
                self::$config['dsn'],
                self::$config['user'],
                self::$config['pass']
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur de lecture du fichier de configuration");
        }
        
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['name']}";
        
        self::$config = [
            'dsn' => $dsn,
            'user' => $conf['user'],
            'pass' => $conf['pass']
        ];
    }

    public function findAllPlaylists(): array {
        $query = "SELECT id, nom FROM playlist";
        $stmt = $this->db->query($query);
        
        $playlists = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playlist = new Playlist($row['nom']);
            $playlist->setId((int)$row['id']);
            $playlists[] = $playlist;
        }
        return $playlists;
    }

    public function savePlaylist(Playlist $playlist): bool {
        $query = "INSERT INTO playlist (nom) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $playlist->__get('name'));
        return $stmt->execute();
    }

    public function saveTrack(AudioTrack $track): bool {
        $query = "INSERT INTO track (titre, artiste_album, duree, filename) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $track->__get('title'));
        $stmt->bindValue(2, $track->__get('artist'));
        $stmt->bindValue(3, $track->__get('duration'));
        $stmt->bindValue(4, $track->__get('fileName'));
        return $stmt->execute();
    }

    public function addTrackToPlaylist(int $id_playlist, int $id_track): bool {
        $query = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste)
                  VALUES (?, ?, (SELECT * FROM (SELECT IFNULL(MAX(no_piste_dans_liste), 0) + 1 FROM playlist2track WHERE id_pl = ?) AS temp))";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id_playlist);
        $stmt->bindValue(2, $id_track);
        $stmt->bindValue(3, $id_playlist);

        return $stmt->execute();
    }
}