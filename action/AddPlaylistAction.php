<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\Playlist;

class AddPlaylistAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'POST' && !empty($_POST['playlist_name'])) {
            $playlistName = $_POST['playlist_name'];
            $_SESSION['playlist'] = new Playlist($playlistName);
            return "<p>Playlist '$playlistName' créée avec succès !</p>";
        }

        return "<form method='post'>
                    <input type='text' name='playlist_name' placeholder='Nom de la playlist'>
                    <button type='submit'>Créer Playlist</button>
                </form>";
    }
}
