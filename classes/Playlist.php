<?php

namespace iutnc\deefy\classes;

class Playlist extends AudioList {

    public function addTrack(AudioTrack $track): void {
        $this->tracks[] = $track;
        $this->nbTracks = count($this->tracks);
        $this->totalDuration += $track->duration;
    }

    public function removeTrack(int $index): void {
        if (isset($this->tracks[$index])) {
            $this->totalDuration -= $this->tracks[$index]->duration;
            array_splice($this->tracks, $index, 1);
            $this->nbTracks = count($this->tracks);
        }
    }

    public function addTracks(array $tracks): void {
        foreach ($tracks as $newTrack) {
            $exists = false;
            foreach ($this->tracks as $t) {
                if ($t === $newTrack) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $this->tracks[] = $newTrack;
                $this->nbTracks = count($this->tracks);
                $this->totalDuration += $newTrack->duration;
            }
        }
    }

    public function __get(string $attr): mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        return parent::__get($attr);
    }
}
