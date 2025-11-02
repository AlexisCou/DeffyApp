<?php
/*
require_once 'vendor/autoload.php';
session_start();

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\classes\Playlist;
use iutnc\deefy\classes\AudioTrack;

/*
 * =================================================================
 * CODE ORIGINAL MIS EN PAUSE POUR LE TEST
 * =================================================================
 * use iutnc\deefy\dispatch\Dispatcher;
 * $dispatcher = new Dispatcher();
 * $dispatcher->run();
 * =================================================================
 

// =================================================================
// CODE DE TEST POUR L'EXERCICE 3
// =================================================================

// --- Initialisation du Repository ---
try {
    DeefyRepository::setConfig('db.config.ini');
    $repo = DeefyRepository::getInstance();
    echo "<h1>Test de l'Exercice 3</h1>";
} catch (Exception $e) {
    die("<h2>Erreur de connexion</h2><p>Impossible d'initialiser le Repository. Message : " . $e->getMessage() . "</p>");
}

// --- TEST 1 : Afficher les playlists existantes ---
echo "<h2>1. Liste des playlists :</h2>";
try {
    $playlists = $repo->findAllPlaylists();
    if (empty($playlists)) {
        echo "<p>Aucune playlist trouvée.</p>";
    } else {
        echo "<ul>";
        foreach ($playlists as $p) {
            printf("<li>ID %d — %s</li>", $p->__get('id'), $p->__get('name'));
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur lors de la récupération des playlists : " . $e->getMessage() . "</p>";
}
echo "<hr>";


// --- TEST 2 : Ajouter une nouvelle playlist ---
echo "<h2>2. Ajout d'une nouvelle playlist :</h2>";
try {
    $nouvellePlaylist = new Playlist("Ma Playlist de l'IUT");
    // On suppose que savePlaylist a été modifiée pour prendre un $userId (ex: 1 pour le test)
    // Pour que le test fonctionne avec le nouveau Repo, il faut adapter cet appel :
    // if ($repo->savePlaylist($nouvellePlaylist, 1)) { 
    if ($repo->savePlaylist($nouvellePlaylist)) { // Garde l'ancien appel pour ton test
        echo "<p style='color:green;'>La playlist '{$nouvellePlaylist->__get('name')}' a bien été ajoutée !</p>";
    } else {
        echo "<p style='color:red;'>Échec de l'ajout de la playlist.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur lors de la sauvegarde de la playlist : " . $e->getMessage() . "</p>";
}
echo "<hr>";


// --- TEST 3 : Ajouter une nouvelle piste ---
echo "<h2>3. Ajout d'une nouvelle piste :</h2>";
try {
    $nouvellePiste = new AudioTrack("Son du TD", "td13.mp3");
    $nouvellePiste->setArtist("Le Prof");
    $nouvellePiste->setDuration(120);
    if ($repo->saveTrack($nouvellePiste)) {
        echo "<p style='color:green;'>La piste '{$nouvellePiste->__get('title')}' a bien été ajoutée !</p>";
    } else {
        echo "<p style='color:red;'>Échec de l'ajout de la piste.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur lors de la sauvegarde de la piste : " . $e->getMessage() . "</p>";
}
echo "<hr>";


// --- TEST 4 : Lier la piste 1 à la playlist 1 ---
echo "<h2>4. Ajout de la piste ID=10 à la playlist ID=5 :</h2>";
try {
    if ($repo->addTrackToPlaylist(5, 10)) {
        echo "<p style='color:green;'>La piste a bien été ajoutée à la playlist !</p>";
    } else {
        echo "<p style='color:red;'>Échec de l'ajout de la piste à la playlist.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur lors de l'ajout de la piste à la playlist : " . $e->getMessage() . "</p>";
}
*/

// =================================================================
// CODE DE PRODUCTION (MODIFIÉ)
// =================================================================

require_once 'vendor/autoload.php';
session_start();

// On importe les classes Dispatcher ET Repository
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository; // <-- 1. AJOUTÉ

try {
    // 2. AJOUTÉ : On charge la configuration AVANT tout le reste
    DeefyRepository::setConfig('db.config.ini');

} catch (\Exception $e) {
    // Si la config échoue, on arrête tout
    die("<h2>Erreur de configuration</h2><p>Impossible de charger la configuration de la base de données : " . $e->getMessage() . "</p>");
}

// On crée le Dispatcher et on le lance
$dispatcher = new Dispatcher();
$dispatcher->run();