<?php
namespace iutnc\deefy\action;

class DefaultAction extends Action {
    public function execute(): string {
        return "<h1>Bienvenue sur DeefyApp !</h1>
                <p>Utilisez le menu pour afficher ou créer des playlists et ajouter des tracks.</p>";
    }
}
