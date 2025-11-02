<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\PodcastTrack;
use iutnc\deefy\classes\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

/*class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        //session_start();

        // Vérifie qu'une playlist existe déjà
        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist trouvée. <a href='?action=add-playlist'>Créer une playlist</a></p>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<HTML
                <h2>Ajouter une piste audio</h2>
                <form method="post" enctype="multipart/form-data" action="?action=add-track">
                    <label>Titre :</label>
                    <input type="text" name="title" required><br>
                    <label>Auteur :</label>
                    <input type="text" name="author" required><br>
                    <label>Fichier audio (.mp3 uniquement) :</label>
                    <input type="file" name="userfile" accept=".mp3,audio/mpeg" required><br><br>
                    <button type="submit">Ajouter la piste</button>
                </form>
            HTML;
        }

        //$title = filter_var($_POST['title'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //$author = filter_var($_POST['author'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $audioPath = null;

        if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] === UPLOAD_ERR_OK) {

            $tmp = $_FILES['userfile']['tmp_name'];
            $name = $_FILES['userfile']['name'];
            $type = $_FILES['userfile']['type'];
            $ext = strtolower(substr($name, -4));

            // Sécurité : on n'accepte que les fichiers .mp3
            if ($ext === '.mp3' && $type === 'audio/mpeg') {
/*
                $uploadDir = __DIR__ . '/../audio';
                // Crée le dossier audio si besoin
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Nom de fichier unique et sécurisé
                $newName = uniqid('track_', true) . '.mp3';
                $dest = $uploadDir . '/' . $newName;

                // Déplace le fichier depuis le dossier temporaire
                if (move_uploaded_file($tmp, $dest)) {
                    $audioPath = $dest;
                } else {
                    return "<p>Erreur lors du déplacement du fichier.</p>";
                }

        
                // Si le dossier n'existe pas, on le crée avec les bons droits
                if (!is_dir($uploadDir)) {
                    // On tente de créer le dossier sous /var/www/html/audio (toujours accessible en écriture)
                    $fallbackDir = __DIR__ . '/../audio';

                    if (@mkdir($uploadDir, 0775, true)) {
                        chmod($uploadDir, 0775);
                    } elseif (@mkdir($fallbackDir, 0775, true)) {
                        chmod($fallbackDir, 0775);
                        $uploadDir = $fallbackDir; // redirige le stockage vers /var/www/html/audio
                    } else {
                        return "<p>Erreur : impossible de créer le dossier audio (pas de permission).</p>";
                    }
                }

                // Double vérif : le dossier est bien accessible en écriture
                if (!is_writable($uploadDir)) {
                    return "<p>Erreur : le dossier audio n'est pas accessible en écriture.</p>";
                }

                // Nom de fichier unique et déplacement
                $newName = uniqid('track_', true) . '.mp3';
                $dest = $uploadDir . '/' . $newName;

                if (move_uploaded_file($tmp, $dest)) {
                    $audioPath = 'audio/' . $newName; // chemin relatif pour le <audio>
                } else {
                    return "<p>Erreur lors du déplacement du fichier.</p>";
                }


               



            } else {
                return "<p>Type de fichier non autorisé. Seuls les fichiers .mp3 sont acceptés.</p>";
            }

        } else {
            return "<p>Aucun fichier uploadé ou erreur lors du transfert.</p>";
        }

        $track = new PodcastTrack($title, $audioPath, $author );

        // Récupère la playlist en session
        $playlist = $_SESSION['playlist'];
        $playlist->addTrack($track);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();

        return <<<HTML
            <h2>Piste ajoutée : {$track->title}</h2>
            <p><strong>Auteur :</strong> {$track->auteur}</p>
            <audio controls src="{$audioPath}"></audio>
            <hr>
            $html
            <a href="?action=add-track">Ajouter encore une piste</a>
        HTML;
    }
}*/

/*class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            return "<p>Veuillez vous connecter pour ajouter une piste.</p>
                    <p><a href='?action=signin'>Connexion</a></p>";
        }

        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findAllPlaylistsByUser($_SESSION['user']['id']);

        // Si l'utilisateur n'a aucune playlist
        if (empty($playlists)) {
            return "<p>Vous devez d'abord créer une playlist avant d'ajouter une piste.</p>
                    <p><a href='?action=add-playlist'>Créer une playlist</a></p>";
        }

        // --- Formulaire (GET)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $options = '';
            foreach ($playlists as $pl) {
                $options .= "<option value='{$pl->__get('id')}'>{$pl->__get('name')}</option>";
            }

            return <<<HTML
                <h2>Ajouter une piste audio</h2>
                <form method="post" enctype="multipart/form-data" action="?action=add-track">
                    <p><label>Titre :</label>
                    <input type="text" name="title" required></p>

                    <p><label>Auteur :</label>
                    <input type="text" name="author" required></p>

                    <p><label>Fichier audio (.mp3 uniquement) :</label>
                    <input type="file" name="userfile" accept=".mp3,audio/mpeg" required></p>

                    <p><label>Ajouter à la playlist :</label>
                    <select name="playlist_id" required>
                        $options
                    </select></p>

                    <button type="submit">Ajouter la piste</button>
                </form>
            HTML;
        }

        // --- Traitement du formulaire (POST)
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $playlistId = (int)($_POST['playlist_id'] ?? 0);

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return "<p>Erreur : aucun fichier reçu ou problème d'upload.</p>";
        }

        $uploadDir = '/var/www/html/audio';

        // Vérifie si le dossier existe, sinon le créer avec les bonnes permissions
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
            // Donne les droits à www-data (si le script tourne pas déjà sous cet utilisateur)
            @chown($uploadDir, 'www-data');
        }

        // Vérifie que le dossier est bien accessible
        if (!is_writable($uploadDir)) {
            return "<p>Erreur : le dossier audio n'est pas accessible en écriture.</p>";
        }

        $name = $_FILES['userfile']['name'];
        $type = $_FILES['userfile']['type'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($ext !== 'mp3' || $type !== 'audio/mpeg') {
            return "<p>Erreur : seul les fichiers .mp3 sont acceptés.</p>";
        }

        // On garde uniquement le nom du fichier (sans déplacement)
        $fileName = basename($name);

        // Création et sauvegarde dans la BD
        $track = new PodcastTrack($title, $fileName, $author);
        $repo->saveTrack($track);
        $repo->addTrackToPlaylist($playlistId, $repo->getLastInsertId());

        return <<<HTML
            <h2>Piste ajoutée avec succès 🎵</h2>
            <p><strong>Titre :</strong> $title</p>
            <p><strong>Auteur :</strong> $author</p>
            <p><strong>Fichier :</strong> $fileName</p>
            <p><a href="?action=add-track">Ajouter une autre piste</a></p>
        HTML;
    }
}*/

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            return "<p>Veuillez vous connecter pour ajouter une piste.</p>
                    <p><a href='?action=signin'>Connexion</a></p>";
        }

        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findAllPlaylistsByUser($_SESSION['user']['id']);

        if (empty($playlists)) {
            return "<p>Vous devez d'abord créer une playlist avant d'ajouter une piste.</p>
                    <p><a href='?action=add-playlist'>Créer une playlist</a></p>";
        }

        // --- Formulaire (GET)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $options = '';
            foreach ($playlists as $pl) {
                $options .= "<option value='{$pl->__get('id')}'>{$pl->__get('name')}</option>";
            }

            return <<<HTML
                <h2>Ajouter une piste audio</h2>
                <form method="post" enctype="multipart/form-data" action="?action=add-track">
                    <p><label>Titre :</label>
                    <input type="text" name="title" required></p>

                    <p><label>Auteur :</label>
                    <input type="text" name="author" required></p>

                    <p><label>Fichier audio (.mp3 uniquement) :</label>
                    <input type="file" name="userfile" accept=".mp3,audio/mpeg" required></p>

                    <p><label>Ajouter à la playlist :</label>
                    <select name="playlist_id" required>
                        $options
                    </select></p>

                    <button type="submit">Ajouter la piste</button>
                </form>
            HTML;
        }

        // --- POST : traitement
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $playlistId = (int)($_POST['playlist_id'] ?? 0);

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return "<p>Erreur : aucun fichier reçu ou problème d'upload.</p>";
        }

        $tmpFile = $_FILES['userfile']['tmp_name'];
        $name = $_FILES['userfile']['name'];
        $type = $_FILES['userfile']['type'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($ext !== 'mp3' || $type !== 'audio/mpeg') {
            return "<p>Erreur : seuls les fichiers .mp3 sont acceptés.</p>";
        }

        // 1️⃣ Lire le contenu du fichier
        $data = file_get_contents($tmpFile);
        if ($data === false) {
            return "<p>Erreur : impossible de lire le fichier.</p>";
        }

        // 2️⃣ Sauvegarder le fichier dans la table audio_file
        $audioId = $repo->saveAudioFile($name, $type, $data);

        // 3️⃣ Créer la track dans la table track
        $track = new PodcastTrack($title, $name, $author);
        $repo->saveTrack($track);
        $trackId = $repo->getLastInsertId();

        // 4️⃣ Lier track ↔ audio_file
        $repo->linkTrackToAudio($trackId, $audioId);

        // 5️⃣ Lier la track à la playlist choisie
        $repo->addTrackToPlaylist($playlistId, $trackId);

        return <<<HTML
            <h2>Piste ajoutée avec succès 🎵</h2>
            <p><strong>Titre :</strong> {$title}</p>
            <p><strong>Auteur :</strong> {$author}</p>
            <p><strong>Fichier enregistré dans la base :</strong> {$name}</p>
            <p><a href="?action=add-track">Ajouter une autre piste</a></p>
        HTML;
    }
}
