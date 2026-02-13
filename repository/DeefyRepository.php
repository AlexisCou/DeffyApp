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

    public function findUserByEmail(string $email): array|false {
        $query = "SELECT id, email, passwd, role FROM User WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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



    public function findAllPlaylistsByUser(int $user_id): array {
        $query = "SELECT p.id, p.nom 
                FROM playlist p
                JOIN user2playlist u2p ON u2p.id_pl = p.id
                WHERE u2p.id_user = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$user_id]);

        $playlists = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $playlist = new \iutnc\deefy\classes\Playlist($row['nom']);
            $playlist->setId((int)$row['id']);
            $playlists[] = $playlist;
        }
        return $playlists;
    }



    public function findTracksByPlaylist(int $playlist_id): array {
        $query = "
            SELECT t.id, t.titre, t.genre, t.duree, t.filename, 
                t.type, t.artiste_album, t.titre_album, 
                t.annee_album, t.numero_album, 
                t.auteur_podcast, t.date_posdcast
            FROM track t
            INNER JOIN playlist2track p2t ON t.id = p2t.id_track
            WHERE p2t.id_pl = ?
            ORDER BY p2t.no_piste_dans_liste
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$playlist_id]);

        $tracks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // On choisit la bonne classe selon le type de track
            if ($row['type'] === 'P') {
                $track = new \iutnc\deefy\classes\PodcastTrack(
                    $row['titre'],
                    $row['filename'],
                    $row['auteur_podcast']
                );
            } else {
                $track = new \iutnc\deefy\classes\AudioTrack(
                    $row['titre'],
                    $row['filename']
                );
                // ✅ on évite le TypeError ici
                $track->setArtist($row['artiste_album'] ?? '');
                $track->setDuration((int)$row['duree']);
            }

            $track->setId((int)$row['id']);
            $tracks[] = $track;
        }

        return $tracks;
    }


    public function savePlaylist(Playlist $playlist, int $user_id): bool {
        try {

            $query = "INSERT INTO playlist (nom) VALUES (?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1, $playlist->__get('name'));
            $stmt->execute();

            $playlist_id = (int) $this->db->lastInsertId();

            $linkQuery = "INSERT INTO user2playlist (id_user, id_pl) VALUES (?, ?)";
            $stmt2 = $this->db->prepare($linkQuery);
            $stmt2->execute([$user_id, $playlist_id]);

            return true;

        } catch (\PDOException $e) {
            error_log("Erreur lors de la sauvegarde de la playlist : " . $e->getMessage());
            return false;
        }
    }


    public function saveTrack(AudioTrack $track): bool {
        $query = "INSERT INTO track (titre, artiste_album, duree, filename) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $track->__get('title'));
        $stmt->bindValue(2, $track->__get('artist') ?? ''); // artiste_album
        $stmt->bindValue(3, $track->getDuration());        // utilise le getter public
        //echo "<script>console.log('Message PHP : " . $track->getDuration() . "');</script>";
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

    public function getLastInsertId(): int {
        return (int)$this->db->lastInsertId();
    }



    public function initializeAudioTables(): void {
        $queries = [

            "CREATE TABLE IF NOT EXISTS track (
                id INT PRIMARY KEY AUTO_INCREMENT,
                titre VARCHAR(255) NOT NULL,
                genre VARCHAR(100),
                duree INT,
                filename VARCHAR(255),
                type CHAR(1) DEFAULT 'A',
                artiste_album VARCHAR(255),
                titre_album VARCHAR(255),
                annee_album INT,
                numero_album INT,
                auteur_podcast VARCHAR(255),
                date_posdcast DATE,
                ENGINE=InnoDB
            )",

            "CREATE TABLE IF NOT EXISTS audio_file (
                id_audio INT PRIMARY KEY AUTO_INCREMENT,
                filename VARCHAR(255) NOT NULL,
                data LONGBLOB NOT NULL,
                mime_type VARCHAR(50) DEFAULT 'audio/mpeg',
                ENGINE=InnoDB
            )",

            "CREATE TABLE IF NOT EXISTS track_audio (
                id_track INT NOT NULL,
                id_audio INT NOT NULL,
                PRIMARY KEY (id_track, id_audio),
                FOREIGN KEY (id_track) REFERENCES track(id) ON DELETE CASCADE,
                FOREIGN KEY (id_audio) REFERENCES audio_file(id_audio) ON DELETE CASCADE,
                ENGINE=InnoDB
            )"
        ];

        foreach ($queries as $sql) {
            $this->db->exec($sql);
        }
    }

    public function saveAudioFile(string $filename, string $mime, string $data): int {
        $stmt = $this->db->prepare("INSERT INTO audio_file (filename, mime_type, data) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $filename);
        $stmt->bindParam(2, $mime);
        $stmt->bindParam(3, $data, \PDO::PARAM_LOB);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    public function linkTrackToAudio(int $trackId, int $audioId): bool {
        $stmt = $this->db->prepare("INSERT INTO track_audio (id_track, id_audio) VALUES (?, ?)");
        return $stmt->execute([$trackId, $audioId]);
    }

    public function getAudioFileById(int $id_audio): ?array {
        $stmt = $this->db->prepare("SELECT filename, mime_type, data FROM audio_file WHERE id_audio = ?");
        $stmt->execute([$id_audio]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    public function findAudioIdByTrack(int $id_track): ?int {
        $stmt = $this->db->prepare("SELECT id_audio FROM track_audio WHERE id_track = ?");
        $stmt->execute([$id_track]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['id_audio'] : null;
    }
}