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

        // --- FORMULAIRE (GET)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $options = '';
            foreach ($playlists as $pl) {
                $options .= "<option value='{$pl->__get('id')}'>{$pl->__get('name')}</option>";
            }

            return <<<HTML
                <h2>Ajouter une piste audio</h2>
                <form method="post" enctype="multipart/form-data" action="?action=add-track" style="margin-left: 40px;">
                    <p><label>Titre :</label><br>
                    <input type="text" name="title" required></p>

                    <p><label>Auteur :</label><br>
                    <input type="text" name="author" required></p>

                    <p><label>Durée (en secondes) :</label><br>
                    <input type="number" name="duration" min="0" required></p>

                    <p><label>Fichier audio (.mp3 uniquement) :</label><br>
                    <input type="file" name="userfile" accept=".mp3,audio/mpeg" required></p>

                    <p><label>Ajouter à la playlist :</label><br>
                    <select name="playlist_id" required>
                        $options
                    </select></p>

                    <button type="submit">Ajouter la piste</button>
                </form>
            HTML;
        }

        // --- TRAITEMENT DU FORMULAIRE (POST)
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $playlistId = (int)($_POST['playlist_id'] ?? 0);

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return "<p>Erreur : aucun fichier reçu ou problème d'upload.</p>";
        }

        $name = $_FILES['userfile']['name'];
        $type = $_FILES['userfile']['type'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if ($ext !== 'mp3' || $type !== 'audio/mpeg') {
            return "<p>Erreur : seuls les fichiers .mp3 sont acceptés.</p>";
        }

        // Lecture du contenu binaire du MP3
        $data = file_get_contents($_FILES['userfile']['tmp_name']);
        $fileName = basename($name);

        // Enregistrement du MP3 dans la table audio_file
        $audioId = $repo->saveAudioFile($fileName, $type, $data);

        // Création de la track
        $track = new PodcastTrack($title, $fileName, $author);
        $track->setDuration($duration);

        // 🔹 Ici on remplit artiste_album avec l'auteur saisi
        $track->setArtist($author);

        // Sauvegarde de la track
        $repo->saveTrack($track);

        // Récupération de l'ID de la track
        $trackId = $repo->getLastInsertId();

        // Lien track <-> audio_file
        $repo->linkTrackToAudio($trackId, $audioId);

        // Lien track <-> playlist
        $repo->addTrackToPlaylist($playlistId, $trackId);

        return <<<HTML
            <h2>Piste ajoutée avec succès 🎵</h2>
            <div style="margin-left: 40px;">
                <p><strong>Titre :</strong> $title</p>
                <p><strong>Auteur :</strong> $author</p>
                <p><strong>Durée :</strong> $duration s</p>
                <p><strong>Fichier :</strong> $fileName</p>
                <p><a href="?action=add-track">Ajouter une autre piste</a></p>
            </div>
        HTML;
    }
}