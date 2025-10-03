<?php

namespace iutnc\deefy\classes;

class Album extends AudioList
{
    private string $artist;
    private string $releaseDate;

    public function __construct(string $name, array $tracks, string $artist = '', string $releaseDate = '') {
        parent::__construct($name, $tracks);
        $this->artist = $artist;
        $this->releaseDate = $releaseDate;
    }

    public function __get(string $attr): mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        return parent::__get($attr);
    }

    public function setArtist(string $artist): void
    {
        $this->artist = $artist;
    }

    public function setReleaseDate(string $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }



}