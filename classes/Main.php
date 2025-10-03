<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use iutnc\deefy\classes\AudioTrack;
use iutnc\deefy\classes\AlbumTrack;
use iutnc\deefy\classes\PodcastTrack;
use iutnc\deefy\classes\Album;
use iutnc\deefy\classes\Playlist;
use iutnc\deefy\classes\AudioListRenderer;
use iutnc\deefy\Exception\InvalidPropertyNameException;
use iutnc\deefy\Exception\InvalidPropertyValueException;

echo "<h1>Exercice 1 : création de pistes</h1>";

// Pistes individuelles
$trackA = new AudioTrack("Enter Sandman", "enter_sandman.mp3");
$trackB = new AlbumTrack("Master of Puppets", "master_of_puppets.mp3", "Master of Puppets", 1);
$trackC = new PodcastTrack("Metallica Story", "metallica_story.mp3", "Metallica Fan");

$trackA->setArtist("Metallica");
$trackA->setGenre("Heavy Metal");
$trackA->setDuration(330);

$trackB->setDuration(515);
$trackC->setDuration(900);

echo $trackA . "<br>";
echo $trackB . "<br>";
echo $trackC . "<br>";

echo "<h1>Exercice 2 : exceptions</h1>";

try {
    $trackA->setDuration(-10);
} catch (InvalidPropertyValueException $e) {
    echo "Exception attrapée : " . $e->getMessage() . "<br>";
}

try {
    echo $trackA->duree;
} catch (InvalidPropertyNameException $e) {
    echo "Exception attrapée : " . $e->getMessage() . "<br>";
}

echo "<h1>Exercice 3 : listes de pistes</h1>";

$albumTracks = [
    new AudioTrack("Battery", "battery.mp3"),
    new AudioTrack("Master of Puppets", "master_of_puppets_album.mp3"),
    new AudioTrack("Welcome Home (Sanitarium)", "sanitarium.mp3")
];
$albumTracks[0]->setDuration(312);
$albumTracks[1]->setDuration(515);
$albumTracks[2]->setDuration(388);

$album = new Album("Master of Puppets", $albumTracks, "Metallica", "1986-03-03");

echo "Album : " . $album->name . " | Artiste : " . $album->artist . " | NbTracks : " . $album->nbTracks . " | Durée totale : " . $album->totalDuration . "<br>";

// Playlist
$playlist = new Playlist("Top Metallica Hits");
$playlist->addTrack($trackA);
$playlist->addTrack($trackB);
$playlist->addTracks($albumTracks);

echo "Playlist : " . $playlist->name . " | NbTracks : " . $playlist->nbTracks . " | Durée totale : " . $playlist->totalDuration . "<br>";

$playlist->removeTrack(0);
echo "Après suppression : NbTracks : " . $playlist->nbTracks . " | Durée totale : " . $playlist->totalDuration . "<br>";

echo "<h1>Exercice 4 : rendu HTML des listes</h1>";

$albumRenderer = new AudioListRenderer($album);
$albumRenderer->render();

$playlistRenderer = new AudioListRenderer($playlist);
$playlistRenderer->render();
