<?php
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class PlayAudioAction extends Action
{
    public function execute(): string
    {
        if (!isset($_GET['id_audio'])) {
            http_response_code(400);
            return "Erreur : identifiant audio manquant.";
        }

        $id_audio = (int)$_GET['id_audio'];

        $repo = DeefyRepository::getInstance();
        $audio = $repo->getAudioFileById($id_audio);

        if (!$audio) {
            http_response_code(404);
            return "Fichier audio introuvable.";
        }

        if (!isset($audio['data'], $audio['mime_type'], $audio['filename'])) {
            http_response_code(500);
            return "Erreur : données audio incomplètes.";
        }

        header("Content-Type: {$audio['mime_type']}");
        header("Content-Disposition: inline; filename=\"{$audio['filename']}\"");
        header("Content-Length: " . strlen($audio['data']));
        header("Accept-Ranges: bytes");

        echo $audio['data'];

        exit;
    }
}