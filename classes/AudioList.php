<?php

namespace iutnc\deefy\classes;

class AudioList {
    protected string $name;
    protected array $tracks;
    protected int $nbTracks;
    protected int $totalDuration;

    public function __construct(string $name, array $tracks = []) {
        $this->name = $name;
        $this->tracks = $tracks;
        $this->nbTracks = count($tracks);
        $this->totalDuration = 0;
        foreach ($tracks as $track) {
            $this->totalDuration += $track->duration;
        }
    }

    public function __get(string $attr): mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        throw new Exception("$attr : invalid property");
    }
}
