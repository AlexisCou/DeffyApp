<?php
require_once 'InvalidPropertyNameException.php';
require_once 'InvalidPropertyValueException.php';
require_once 'AudioTrack.php';
require_once 'AlbumTrack.php';
require_once 'PodcastTrack.php';

echo "<h3>Test 1 : durée négative</h3>";
$track1 = new AudioTrack("Titre 1", "fichier1.mp3");
try {
    $track1->setDuration(-10);
} catch (InvalidPropertyValueException $e) {
    echo "Exception attrapée : " . $e->getMessage() . "<br>";
    echo $e->getTraceAsString() . "<br>";
}

echo "<h3>Test 2 : propriété inexistante</h3>";
$track2 = new AudioTrack("Titre 2", "fichier2.mp3");
try {
    echo $track2->duree;
} catch (InvalidPropertyNameException $e) {
    echo "Exception attrapée : " . $e->getMessage() . "<br>";
    echo $e->getTraceAsString() . "<br>";
}
