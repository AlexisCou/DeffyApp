<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\AlbumTrack;
use iutnc\deefy\classes\PodcastTrack;
use iutnc\deefy\classes\Playlist;

class AddPodcastTrackAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['playlist'])) {
            $_SESSION['playlist'] = new Playlist("Ma Playlist");
        }
        $playlist = $_SESSION['playlist'];

        if ($this->http_method === 'POST' && !empty($_POST['track_name']) && !empty($_POST['track_type'])) {
            $trackName = $_POST['track_name'];
            $trackType = $_POST['track_type']; // "album" ou "podcast"

            if ($trackType === 'album') {
                $track = new AlbumTrack($trackName, $trackName . ".mp3", "Album X", 1);
                $track->setDuration(300); // durée exemple
            } else {
                $track = new PodcastTrack($trackName, $trackName . ".mp3", "Auteur Y");
                $track->setDuration(600); // durée exemple
            }

            $playlist->addTrack($track);
            $_SESSION['playlist'] = $playlist; // sauvegarde
            return "<p>Track '$trackName' ajouté à la playlist !</p>";
        }

        return "<form method='post'>
                    <input type='text' name='track_name' placeholder='Nom du track'>
                    <select name='track_type'>
                        <option value='album'>Album</option>
                        <option value='podcast'>Podcast</option>
                    </select>
                    <button type='submit'>Ajouter Track</button>
                </form>";
    }
}
