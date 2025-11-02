<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\classes\AudioListRenderer;

class AddPlaylistAction extends Action {
    public function execute(): string {

        // debug temporaire — retirer après test
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        error_log('SESSION DEBUG: ' . print_r($_SESSION, true));

        
        if ($this->http_method === 'POST') {
            $name = $_POST['playlist_name'] ?? '';
            $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($name)) {
                $playlist = new Playlist($name);
                if (!isset($_SESSION['user'])) {
                    return "<p>Erreur : vous devez être connecté pour créer une playlist.</p>
                            <p><a href='?action=signin'>Se connecter</a></p>";
                }
                $repo = DeefyRepository::getInstance();
                $repo->savePlaylist($playlist, $_SESSION['user']['id']);

                return $htmlList . "<p><a href='?action=add-track'>Ajouter une piste</a></p>";
            } else {
                return "<p>Nom de playlist invalide.</p>" . $this->renderForm();
            }
        }

        return $this->renderForm();
    }

    private function renderForm(): string {
        return <<<HTML
        <form method="post" action="?action=add-playlist">
            <label>Nom de la playlist : <input type="text" name="playlist_name" required></label>
            <button type="submit">Créer Playlist</button>
        </form>
        HTML;
    }
}