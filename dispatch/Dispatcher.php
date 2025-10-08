<?php
namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\{
    DefaultAction,
    DisplayPlaylistAction,
    AddPlaylistAction,
    AddPodcastTrackAction,
    AddUserAction
};

class Dispatcher {
    private string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {
        switch ($this->action) {
            case 'playlist':
                $action = new DisplayPlaylistAction();
                break;
            case 'add-playlist':
                $action = new AddPlaylistAction();
                break;
            case 'add-track':
                $action = new AddPodcastTrackAction();
                break;
            case 'add-user':
                $action = new AddUserAction();
                break;
            default:
                $action = new DefaultAction();
                break;
        }

        $html = $action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Deefy App</title>
        </head>
        <body>
            <h1>DeefyApp</h1>
            <nav>
                <a href="?action=default">Accueil</a> |
                <a href="?action=playlist">Voir Playlist</a> |
                <a href="?action=add-playlist">Créer Playlist</a> |
                <a href="?action=add-track">Ajouter Track</a> |
                <a href="?action=add-user">Inscription</a>
            </nav>
            <hr>
            $html
        </body>
        </html>
        HTML;
    }
}

