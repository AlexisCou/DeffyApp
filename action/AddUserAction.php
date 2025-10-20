<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\AuthnException;

class AddUserAction extends Action {

    public function execute(): string {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            
            $html = '<h2>Inscription</h2>
            <form method="POST">
                <p>
                    <label for="email">Email :</label>
                    <input type="email" name="email" id="email" required>
                </p>
                <p>
                    <label for="passwd">Mot de passe (10 caractères min.) :</label>
                    <input type="password" name="passwd" id="passwd" required>
                </p>
                <p>
                    <label for="passwd_confirm">Confirmer le mot de passe :</label>
                    <input type="password" name="passwd_confirm" id="passwd_confirm" required>
                </p>
                <p>
                    <button type="submit">S\'inscrire</button>
                </p>
            </form>';
            
            return $html;

        } else {

            $email = $_POST['email'] ?? '';
            $passwd = $_POST['passwd'] ?? '';
            $passwd_confirm = $_POST['passwd_confirm'] ?? '';

            if ($passwd !== $passwd_confirm) {
                return "<h2>Erreur d'inscription</h2>
                        <p>Les deux mots de passe ne correspondent pas.</p>
                        <p><a href='?action=add-user'>Réessayer</a></p>";
            }

            try {
                AuthnProvider::register($email, $passwd);
                
                $html = "<h2>Inscription réussie !</h2>
                         <p>Votre compte a été créé. Vous pouvez maintenant vous connecter.</p>
                         <p><a href='?action=signin'>Se connecter</a></p>";
                
                return $html;

            } catch (AuthnException $e) {
                $html = "<h2>Échec de l'inscription</h2>
                         <p>" . $e->getMessage() . "</p>
                         <p><a href='?action=add-user'>Réessayer</a></p>";
                
                return $html;
            }
        }
    }
}