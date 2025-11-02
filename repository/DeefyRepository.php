<?php
declare(strict_types=1);

namespace iutnc\deefy\repository;

use iutnc\deefy\classes\AudioTrack;
use iutnc\deefy\classes\Playlist;
use iutnc\deefy\classes\PodcastTrack; 
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

    public function findUserByEmail(string $email) : array | false {
        $query = "SELECT id, email, passwd FROM User WHERE email = ?"; 
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user;
    }

    public function findPlaylistsForUser(int $userId): array {
        $query = "SELECT p.id, p.nom FROM playlist p
                  JOIN user2playlist u2p ON p.id = u2p.id_pl
                  WHERE u2p.id_user = ?";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        
        $playlists = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playlist = new Playlist($row['nom']);
            $playlist->setId((int)$row['id']);
            $playlists[] = $playlist;
        }
        return $playlists;
    }

    public function findPlaylistById(int $playlistId): Playlist {
        $queryPl = "SELECT id, nom FROM playlist WHERE id = ?";
        $stmtPl = $this->db->prepare($queryPl);
        $stmtPl->execute([$playlistId]);
        $plData = $stmtPl->fetch(PDO::FETCH_ASSOC);

        if ($plData === false) {
            throw new \Exception("Playlist non trouvée");
        }
        
        $playlist = new Playlist($plData['nom']);
        $playlist->setId((int)$plData['id']);

        $queryTr = "SELECT t.* FROM track t 
                    JOIN playlist2track p2t ON t.id = p2t.id_track 
                    WHERE p2t.id_pl = ? 
                    ORDER BY p2t.no_piste_dans_liste ASC";
        
        $stmtTr = $this->db->prepare($queryTr);
        $stmtTr->execute([$playlistId]);

        while ($trackData = $stmtTr->fetch(PDO::FETCH_ASSOC)) {
            $track = new PodcastTrack($trackData['titre'], $trackData['filename'], $trackData['artiste_album'] ?? $trackData['auteur_podcast'] ?? 'Inconnu');
            $track->setId((int)$trackData['id']);
            $track->setDuration((int)$trackData['duree']);
            
            $playlist->addTrack($track);
        }
        return $playlist;
    }


    public function savePlaylist(Playlist $playlist, int $userId): bool {
        $queryPl = "INSERT INTO playlist (nom) VALUES (?)";
        $stmtPl = $this->db->prepare($queryPl);
        $stmtPl->bindValue(1, $playlist->__get('name'));
        $result = $stmtPl->execute();
        
        $lastId = $this->db->lastInsertId();
        $playlist->setId((int)$lastId); 
        
        $queryU2P = "INSERT INTO user2playlist (id_user, id_pl) VALUES (?, ?)";
        $stmtU2P = $this->db->prepare($queryU2P);
        $stmtU2P->bindValue(1, $userId);
        $stmtU2P->bindValue(2, $lastId);
        $stmtU2P->execute();
        
        return $result;
    }

    public function saveTrack(AudioTrack $track): bool {
        $query = "INSERT INTO track (titre, artiste_album, duree, filename) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $track->__get('title'));
        $stmt->bindValue(2, $track->__get('artist'));
        $stmt->bindValue(3, $track->__get('duration'));
        $stmt->bindValue(4, $track->__get('fileName'));
        
        $result = $stmt->execute();

        $lastId = $this->db->lastInsertId();
        $track->setId((int)$lastId); 
        
        return $result;
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

    public function saveUser(string $email, string $hashed_password): bool {
        $query = "INSERT INTO User (email, passwd, role) VALUES (?, ?, 1)"; 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $email);
        $stmt->bindValue(2, $hashed_password);
        return $stmt->execute();
    }

    public function isPlaylistOwner(int $playlistId, int $userId): bool {
        $query = "SELECT COUNT(*) FROM user2playlist WHERE id_pl = ? AND id_user = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$playlistId, $userId]);
        $count = $stmt->fetchColumn();
        
        return $count > 0;
    }
}