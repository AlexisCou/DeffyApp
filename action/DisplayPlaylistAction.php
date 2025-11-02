<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {
    public function execute(): string {
        
        if (!isset($_SESSION['user'])) {
            header('Location: ?action=signin');
            return "<h2>Accès refusé</h2><p>Vous devez être connecté.</p>";
        }

        $userId = $_SESSION['user']['id'];

        $playlistId = $_GET['id_pl'] ?? null;
        if ($playlistId === null) {
            if (isset($_SESSION['playlist'])) {
                $playlist = $_SESSION['playlist'];
            } else {
                return "<p>Aucune playlist sélectionnée. <a href='?action=my-playlists'>Voir mes playlists</a></p>";
            }
        } else {
            try {
                $repo = DeefyRepository::getInstance();
                
                if (!$repo->isPlaylistOwner((int)$playlistId, $userId)) {
                    return "<h2>Accès refusé</h2><p>Vous n'êtes pas le propriétaire de cette playlist.</p>";
                }

                $playlist = $repo->findPlaylistById((int)$playlistId); 

                $_SESSION['playlist'] = $playlist;
            
            } catch (\Exception $e) {
                return "<h2>Erreur</h2><p>Impossible de charger cette playlist : " . $e->getMessage() . "</p>";
            }
        }
        
        $renderer = new AudioListRenderer($playlist);
        ob_start();
        $renderer->render();
        return ob_get_clean();
    }
}