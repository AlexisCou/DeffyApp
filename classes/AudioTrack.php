<?php
declare(strict_types=1);

namespace iutnc\deefy\classes;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {

    private ?int $id = null;

    protected string $title;
    private string $fileName;

    private string $artist = '';
    private string $genre = '';
    private int $duration = 0;
    private ?int $annee = null;

    public function __construct(string $title, string $fileName) {
        $this->title = $title;
        $this->fileName = $fileName;
    }

    public function __get(string $attr): mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        throw new InvalidPropertyNameException("$attr : invalid property");
    }

    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setAnnee(int $annee): void {
        $this->annee = $annee;
    }

    public function setDuration(int $d): void {
        if ($d < 0) {
            throw new InvalidPropertyValueException("invalid value for duration : $d");
        }
        $this->duration = $d;
    }

    public function setArtist(string $a): void { $this->artist = $a; }
    public function setGenre(string $g): void { $this->genre = $g; }

    public function __toString(): string {
        return json_encode([
            'id' => $this->id,
            'title' => $this->title,
            'fileName' => $this->fileName,
            'artist' => $this->artist,
            'genre' => $this->genre,
            'duration' => $this->duration,
            'annee' => $this->annee
        ]);
    }
}