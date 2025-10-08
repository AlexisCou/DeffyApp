<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\AudioListRenderer;

class DisplayPlaylistAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist disponible. Ajoutez-en une d'abord.</p>";
        }

        $playlist = $_SESSION['playlist'];
        $renderer = new AudioListRenderer($playlist);
        ob_start();
        $renderer->render();
        return ob_get_clean();
    }
}
