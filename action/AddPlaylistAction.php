<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\Playlist;
use iutnc\deefy\classes\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action {
    public function execute(): string {
        
        if (!isset($_SESSION['user'])) {
            header('Location: ?action=signin');
            return "<h2>Accès refusé</h2>
                    <p>Vous devez être connecté pour accéder à cette page.</p>
                    <p><a href='?action=signin'>Se connecter</a></p>";
        }

        if ($this->http_method === 'POST') {
            $name = $_POST['playlist_name'] ?? '';
            $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!empty($name)) {
                $playlist = new Playlist($name);

                try {
                    $userId = $_SESSION['user']['id']; 

                    $repo = DeefyRepository::getInstance();
                    
                    $repo->savePlaylist($playlist, $userId); 
                
                } catch (\Exception $e) {
                    return "<h2>Erreur de base de données</h2><p>Impossible de sauvegarder la playlist : " . $e->getMessage() . "</p>";
                }

                $_SESSION['playlist'] = $playlist;

                $renderer = new AudioListRenderer($playlist);
                ob_start();
                $renderer->render();
                $htmlList = ob_get_clean();

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