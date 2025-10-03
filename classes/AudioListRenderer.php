<?php

namespace iutnc\deefy\classes;

class AudioListRenderer {
    private AudioList $audioList;

    public function __construct(AudioList $list) {
        $this->audioList = $list;
    }

    public function render(): void {
        echo "<h3>" . $this->audioList->name . "</h3>";
        echo "<ul>";
        foreach ($this->audioList->tracks as $track) {
            try {
                $title = $track->__get('title');
                $duration = $track->__get('duration');
            } catch (Exception $e) {
                $title = "(titre inconnu)";
                $duration = 0;
            }

            if ($track instanceof AlbumTrack) {
                echo "<li>" . $title . " - Album: " . $track->__get('album') . " (" . $duration . "s)</li>";
            } elseif ($track instanceof PodcastTrack) {
                echo "<li>" . $title . " - Podcast: " . $track->__get('auteur') . " (" . $duration . "s)</li>";
            } else {
                echo "<li>" . $title . " (" . $duration . "s)</li>";
            }
        }
        echo "</ul>";
        echo "<strong>Nombre de pistes :</strong> " . $this->audioList->nbTracks . "<br>";
        echo "<strong>Durée totale :</strong> " . $this->audioList->totalDuration . "s<br>";
    }

}
