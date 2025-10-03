<?php
declare(strict_types=1);

namespace iutnc\deefy\classes;

class AlbumTrack extends AudioTrack {
    private string $album;
    private int $trackNumber;

    private int $year = 0;
    private int $duration = 0;

    public function __construct(string $title, string $fileName, string $album, int $trackNumber){
        parent::__construct($title, $fileName);
        $this->album = $album;
        $this->trackNumber = $trackNumber;
    }

    public function __get(string $name): mixed{
        if (!property_exists($this, $name)) {
            throw new Exception("invalid property : $name");
        }
        return $this->$name;
    }

    public function setYear(int $year): void{
        $this->year = $year;
    }

    public function setDuration(int $duration): void{
        $this->duration = $duration;
    }

    public function __toString(): string{
        return json_encode(get_object_vars($this));
    }
}
