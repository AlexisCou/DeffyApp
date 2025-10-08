<?php
namespace iutnc\deefy\action;

class AddUserAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'POST') {
            $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $age = filter_var($_POST['age'] ?? '', FILTER_SANITIZE_NUMBER_INT);

            return <<<HTML
            <p>Nom : $nom</p>
            <p>Email : $email</p>
            <p>Âge : {$age} ans</p>
            HTML;
        }

        return <<<HTML
        <form method="post" action="?action=add-user">
            <label>Nom : <input type="text" name="nom" required></label><br>
            <label>Email : <input type="email" name="email" required></label><br>
            <label>Âge : <input type="number" name="age" min="0" required></label><br>
            <button type="submit">S'inscrire</button>
        </form>
        HTML;
    }
}