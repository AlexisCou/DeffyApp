<?php
namespace iutnc\deefy\action;

use iutnc\deefy\classes\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['user'])) {
            return "<p>Veuillez vous connecter pour voir vos playlists.</p>
                    <p><a href='?action=signin'>Connexion</a></p>";
        }

        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findAllPlaylistsByUser($_SESSION['user']['id']);

        if (empty($playlists)) {
            return "<h2>Vos Playlists</h2><hr><p>Aucune playlist trouvée.</p>";
        }

        $html = "<h2>Vos Playlists</h2><hr>";

        foreach ($playlists as $playlist) {

            $tracks = $repo->findTracksByPlaylist($playlist->__get('id'));
            $playlist->addTracks($tracks);

            $tracksHTML = "<ul style='margin-left:20px;'>";
            if (empty($tracks)) {
                $tracksHTML .= "<li><em>Aucune piste dans cette playlist.</em></li>";
            } else {
                foreach ($tracks as $t) {
                    $audioId = $repo->findAudioIdByTrack($t->__get('id'));

                    $audioPlayer = $audioId
                        ? "<audio controls style='margin-top:5px;' src='?action=play&id_audio={$audioId}'></audio>"
                        : "<em>(Pas de fichier audio)</em>";

                    $tracksHTML .= <<<HTML
                        <li>
                            <strong>{$t->__get('title')}</strong> — {$t->__get('artist')} ({$t->__get('duration')}s)
                            <br>$audioPlayer
                        </li>
                    HTML;
                }
            }
            $tracksHTML .= "</ul>";

            $html .= <<<HTML
                <div style="border:1px solid #bbb; padding:10px; margin:15px 0; border-radius:6px;">
                    <h3 style="margin-bottom:10px;">{$playlist->__get('name')}</h3>
                    $tracksHTML
                    <div style="margin-left:15px; margin-top:10px;">
                        <p><strong>Nombre de pistes :</strong> {$playlist->__get('nbTracks')}</p>
                        <p><strong>Durée totale :</strong> {$playlist->__get('totalDuration')} secondes</p>
                    </div>
                </div>
            HTML;
        }

        return $html;
    }
}