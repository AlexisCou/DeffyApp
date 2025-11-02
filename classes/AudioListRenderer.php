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
                $file = $track->__get('fileName') ?? null; // chemin du fichier audio
            } catch (\Exception $e) {
                $title = "(titre inconnu)";
                $duration = 0;
                $file = null;
            }

            /*if ($track instanceof AlbumTrack) {
                echo "<li>{$title} - Album: " . $track->__get('album') . " ({$duration}s)</li>";
            } elseif ($track instanceof PodcastTrack) {
                echo "<li>{$title} - Podcast: " . $track->__get('auteur') . " ({$duration}s)";
                if ($file) {
                    echo "<br><audio controls src=\"{$file}\"></audio>";
                }
                echo "</li>";
            } else {
                echo "<li>{$title} ({$duration}s)";
                if ($file) {
                    echo "<br><audio controls src=\"{$file}\"></audio>";
                }
                echo "</li>";
            }*/

            if ($track instanceof PodcastTrack) {
                echo "<li>{$title} - Podcast: " . $track->__get('auteur') . " ({$duration}s)";
                echo "<br><audio controls src=\"{$track->__get('fileName')}\"></audio>";
                echo "</li>";
            }
        }
        echo "</ul>";
        echo "<strong>Nombre de pistes :</strong> " . $this->audioList->nbTracks . "<br>";
        echo "<strong>Durée totale :</strong> " . $this->audioList->totalDuration . "s<br>";
    }
}
