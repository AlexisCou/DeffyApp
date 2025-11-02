<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

class SignoutAction extends Action {

    public function execute(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        session_destroy();

        header('Location: ?action=default');
        exit;
    }
}