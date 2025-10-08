<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\PodcastTrack;
use iutnc\deefy\classes\AudioListRenderer;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        session_start();

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

        $title = filter_var($_POST['title'] ?? '', FILTER_SANITIZE_STRING);
        $author = filter_var($_POST['author'] ?? '', FILTER_SANITIZE_STRING);
        $audioPath = null;

        if (isset($_FILES['userfile']) && $_FILES['userfile']['error'] === UPLOAD_ERR_OK) {

            $tmp = $_FILES['userfile']['tmp_name'];
            $name = $_FILES['userfile']['name'];
            $type = $_FILES['userfile']['type'];
            $ext = strtolower(substr($name, -4));

            // Sécurité : on n'accepte que les fichiers .mp3
            if ($ext === '.mp3' && $type === 'audio/mpeg') {

                // Crée le dossier audio si besoin
                if (!is_dir('audio')) {
                    mkdir('audio', 0777, true);
                }

                // Nom de fichier unique et sécurisé
                $newName = uniqid('track_', true) . '.mp3';
                $dest = 'audio/' . $newName;

                // Déplace le fichier depuis le dossier temporaire
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

        $track = new PodcastTrack($title, $author, $audioPath);

        // Récupère la playlist en session
        $playlist = $_SESSION['playlist'];
        $playlist->addTrack($track);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();

        return <<<HTML
            <h2>Piste ajoutée : {$track->getTitle()}</h2>
            <p><strong>Auteur :</strong> {$track->getAuthor()}</p>
            <audio controls src="{$audioPath}"></audio>
            <hr>
            $html
            <a href="?action=add-track">Ajouter encore une piste</a>
        HTML;
    }
}
