<?php
declare(strict_types=1);

namespace iutnc\deefy\auth;

use iutnc\deefy\repository\DeefyRepository;

class AuthnProvider {

    public static function signin(string $email, string $passwd2check): void {

        $repo = DeefyRepository::getInstance();

        $user = $repo->findUserByEmail($email);

        if ($user === false) {
            throw new AuthnException("Authentification échouée : email inconnu.");
        }

        $hashed_password = $user['passwd'];

        if (!password_verify($passwd2check, $hashed_password)) {
            throw new AuthnException("Authentification échouée : mot de passe incorrect.");
        }

        unset($user['passwd']); 
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

    }



    public static function register(string $email, string $pass): void {
        

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthnException("Inscription échouée : l'email est invalide.");
        }
        
        if (strlen($pass) < 10) {
            throw new AuthnException("Inscription échouée : le mot de passe doit faire au moins 10 caractères.");
        }

        $repo = DeefyRepository::getInstance();

        $user = $repo->findUserByEmail($email);
        if ($user !== false) {
            throw new AuthnException("Inscription échouée : cet email est déjà utilisé.");
        }

        $hashed_password = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

        $repo->saveUser($email, $hashed_password);
    }
}