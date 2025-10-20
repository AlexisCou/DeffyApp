<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\AuthnException;

class SigninAction extends Action {

    public function execute(): string {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            
            $html = '<h2>Connexion</h2>
            <form method="POST">
                <p>
                    <label for="email">Email :</label>
                    <input type="email" name="email" id="email" placeholder="user1@mail.com" required>
                </p>
                <p>
                    <label for="passwd">Mot de passe :</label>
                    <input type="password" name="passwd" id="passwd" placeholder="user1" required>
                </p>
                <p>
                    <button type="submit">Se connecter</button>
                </p>
            </form>';
            
            return $html;

        } else {

            $email = $_POST['email'] ?? '';
            $passwd = $_POST['passwd'] ?? '';

            try {
                AuthnProvider::signin($email, $passwd);
                
                $html = "<h2>Bienvenue !</h2>
                         <p>Vous êtes maintenant connecté.</p>";
                
                return $html;

            } catch (AuthnException $e) {
                $html = "<h2>Échec de la connexion</h2>
                         <p>Email ou mot de passe incorrect.</p>
                         <p><a href='?action=signin'>Réessayer</a></p>";
                
                return $html;
            }
        }
    }
}