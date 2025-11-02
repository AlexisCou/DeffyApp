<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\PodcastTrack;
use iutnc\deefy\classes\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ?action=signin');
            return "<h2>Accès refusé</h2><p>Vous devez être connecté.</p>";
        }
        
        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist courante trouvée. <a href='?action=my-playlists'>Choisir une playlist</a></p>";
        }

        $playlist = $_SESSION['playlist'];
        $userId = $_SESSION['user']['id'];
        $repo = DeefyRepository::getInstance();

        if (!$repo->isPlaylistOwner($playlist->__get('id'), $userId)) {
            return "<h2>Accès refusé</h2><p>Vous n'êtes pas le propriétaire de cette playlist.</p>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return <<<HTML
                <h2>Ajouter une piste audio</h2>
                <form method="post" enctype="multipart/form-data" action="?action=add-track">
                    <p>
                        <label>Titre :</label>
                        <input type="text" name="title" required><br>
                    </p>
                    <p>
                        <label>Auteur :</label>
                        <input type="text" name="author" required><br>
                    </p>
                    <p>
                        <label>Durée (en secondes) :</label> <input type="number" name="duration" required><br>
                    </p>
                    <p>
                        <label>Fichier audio (.mp3 uniquement) :</label>
                        <input type="file" name="userfile" accept=".mp3,audio/mpeg" required><br><br>
                    </p>
                    <button type="submit">Ajouter la piste</button>
                </form>
            HTML;
        }

        $title = filter_var($_POST['title'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_var($_POST['author'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $duration = filter_var($_POST['duration'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        
        $audioPath = null;

        if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['userfile']['tmp_name'];
            $name = $_FILES['userfile']['name'];
            $type = $_FILES['userfile']['type'];
            $ext = strtolower(substr($name, -4));

            if ($ext === '.mp3' && $type === 'audio/mpeg') {
                if (!is_dir('audio')) {
                    mkdir('audio', 0777, true);
                }
                $newName = uniqid('track_', true) . '.mp3';
                $dest = 'audio/' . $newName;

                if (move_uploaded_file($tmp, $dest)) {
                    $audioPath = $dest;
                } else {
                    return "<p>Erreur lors du déplacement du fichier.</p>";
                }
            } else {
                return "<p>Type de fichier non autorisé. Seuls les fichiers .mp3 sont acceptés.</p>";
            }
        } else {
            return "<p>Aucun fichier uploadé ou erreur lors du transfert.</p>";
        }

        try {
            $track = new PodcastTrack($title, $audioPath, $author);

            $track->setArtist($author); 
            $track->setDuration((int)$duration);

            $repo->saveTrack($track);

            $playlistId = $playlist->__get('id');
            $trackId = $track->__get('id');

            if ($playlistId && $trackId) {
                 $repo->addTrackToPlaylist($playlistId, $trackId);
            } else {
                return "<p>Erreur critique : impossible de lier la piste à la playlist (ID manquant).</p>";
            }

            $playlist->addTrack($track);
            $_SESSION['playlist'] = $playlist;

        } catch (\Exception $e) {
             return "<h2>Erreur de base de données</h2><p>Impossible de sauvegarder la piste : " . $e->getMessage() . "</p>";
        }

        header("Location: ?action=playlist&id_pl={$playlistId}");
        exit;
    }
}