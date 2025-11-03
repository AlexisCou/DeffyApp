<?php
namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\{
    DefaultAction,
    DisplayPlaylistAction,
    AddPlaylistAction,
    AddPodcastTrackAction,
    AddUserAction,
    SigninAction,
    PlayAudioAction,
    LogoutAction
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
            case 'play':
                $action = new PlayAudioAction();
                break;
            case 'logout':
                $action = new LogoutAction();
                break;
            default:
                $action = new DefaultAction();
                break;
        }

        $html = $action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $nav = '';

        if (isset($_SESSION['user'])) {
            $email = $_SESSION['user']['email'];
            $username = explode('@', $email)[0];

            $nav = <<<HTML
                <link rel="stylesheet" href="styles.css">
                <a href="?action=default">Accueil</a> |
                <a href="?action=playlist">Voir Playlist</a> |
                <a href="?action=add-playlist">Créer Playlist</a> |
                <a href="?action=add-track">Ajouter Track</a> |
                <span style="margin-left:20px;">Connecté en tant que <strong>$username</strong></span> |
                <a href="?action=logout">Déconnexion</a>
            HTML;
        } else {
            $nav = <<<HTML
                <link rel="stylesheet" href="styles.css">
                <a href="?action=default">Accueil</a> |
                <a href="?action=playlist">Voir Playlist</a> |
                <a href="?action=add-playlist">Créer Playlist</a> |
                <a href="?action=add-track">Ajouter Track</a> |
                <a href="?action=add-user">Inscription</a> |
                <a href="?action=signin">Connexion</a>
            HTML;
        }

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Deefy App</title>
        </head>
        <body>
            <h1>DeefyApp</h1>
            <nav>$nav</nav>
            <hr>
            $html
        </body>
        </html>
        HTML;
    }

}

