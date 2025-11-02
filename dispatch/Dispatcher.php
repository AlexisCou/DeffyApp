<?php
namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\{
    DefaultAction,
    DisplayPlaylistAction,
    AddPlaylistAction,
    AddPodcastTrackAction,
    AddUserAction,
    SigninAction,
    DisplayMyPlaylistsAction,
    SignoutAction
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
            case 'signin':
                $action = new SigninAction();
                break;
            
            case 'my-playlists':
                $action = new DisplayMyPlaylistsAction();
                break;

            case 'signout':
                $action = new SignoutAction();
                break;

            default:
                $action = new DefaultAction();
                break;
        }

        $html = $action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        
        $authLinks = '';
        if (isset($_SESSION['user'])) {
            $email = $_SESSION['user']['email'];
            $authLinks = "<span>Connecté: $email</span> | <a href='?action=signout'>Déconnexion</a>";
        } else {
            $authLinks = "<a href='?action=add-user'>Inscription</a> | <a href='?action=signin'>Connexion</a>";
        }


        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Deefy App</title>
        </head>
        <body>
            <header style="display:flex; justify-content:space-between; align-items:center;">
                <h1>DeefyApp</h1>
                <nav>
                    <a href="?action=default">Accueil</a> |
                    <a href="?action=my-playlists">Mes Playlists</a> |
                    <a href="?action=add-playlist">Créer Playlist</a> |
                    <a href="?action=add-track">Ajouter Track</a>
                </nav>
                <div style="text-align:right;">
                    $authLinks
                </div>
            </header>
            <hr>
            $html
        </body>
        </html>
        HTML;
    }
}