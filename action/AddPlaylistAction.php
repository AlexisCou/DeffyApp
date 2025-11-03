<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\classes\AudioListRenderer;

class AddPlaylistAction extends Action {
    public function execute(): string {

        if (!isset($_SESSION['user'])) {
            return "<p>Veuillez vous connecter pour créer une playlist.</p>
                    <p><a href='?action=signin'>Connexion</a></p>";
        }

        if ($this->http_method === 'POST') {
            $name = $_POST['playlist_name'] ?? '';
            $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($name)) {
                $playlist = new Playlist($name);
                $repo = DeefyRepository::getInstance();
                $repo->savePlaylist($playlist, $_SESSION['user']['id']);

                return "<p>Playlist <strong>$name</strong> créée avec succès !</p>
                        <p><a href='?action=add-track'>Ajouter une piste</a></p>";
            } else {
                return "<p>Nom de playlist invalide.</p>" . $this->renderForm();
            }
        }

        return $this->renderForm();
    }

    private function renderForm(): string {
        return <<<HTML
        <form method="post" action="?action=add-playlist" style="margin-left:40px;">
            <label>Nom de la playlist :</label><br>
            <input type="text" name="playlist_name" required>
            <br><br>
            <button type="submit">Créer Playlist</button>
        </form>
        HTML;
    }
}