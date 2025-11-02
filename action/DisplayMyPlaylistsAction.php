<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DisplayMyPlaylistsAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user'])) {
            header('Location: ?action=signin');
            return "<h2>Accès refusé</h2><p>Vous devez être connecté.</p>";
        }

        $html = '<h2>Mes Playlists</h2>';
        
        try {
            $userId = $_SESSION['user']['id'];
            $repo = DeefyRepository::getInstance();

            $playlists = $repo->findPlaylistsForUser($userId);

            if (empty($playlists)) {
                $html .= "<p>Vous n'avez encore aucune playlist.</p>";
                $html .= "<p><a href='?action=add-playlist'>Créer ma première playlist</a></p>";
                return $html;
            }

            $html .= '<ul>';
            foreach ($playlists as $p) {
                $playlistId = $p->__get('id');
                $playlistName = $p->__get('name');
                $html .= "<li><a href='?action=playlist&id_pl={$playlistId}'>{$playlistName}</a></li>";
            }
            $html .= '</ul>';

        } catch (\Exception $e) {
            $html .= "<p>Erreur lors du chargement de vos playlists : " . $e->getMessage() . "</p>";
        }

        return $html;
    }
}