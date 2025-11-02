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
                $filePath = $track->__get('fileName');
                $author = '(auteur inconnu)';
                
                if ($track instanceof PodcastTrack) {
                    $author = $track->__get('auteur');
                }

            } catch (\Exception $e) {
                $title = "(titre inconnu)";
                $duration = 0;
                $filePath = '';
                $author = '(erreur)';
            }
            
            echo "<li>";
            if (!empty($filePath)) {
                echo "<p><strong>{$title}</strong></p>";
                echo "<audio controls src='{$filePath}' style='width: 100%; max-width: 400px;'></audio>";
                
                if ($track instanceof PodcastTrack) {
                    echo "<p style='margin-top: 5px; font-size: 0.9em;'>Podcast: {$author} ({$duration}s)</p>";
                } else {
                     echo "<p style='margin-top: 5px; font-size: 0.9em;'>Durée: {$duration}s</p>";
                }

            } else {
                echo "{$title} (Piste introuvable)";
            }
            echo "</li><br>";
        }
        echo "</ul>";
        echo "<strong>Nombre de pistes :</strong> " . $this->audioList->nbTracks . "<br>";
        echo "<strong>Durée totale :</strong> " . $this->audioList->totalDuration . "s<br>";
    }

}