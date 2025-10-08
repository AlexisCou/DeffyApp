<?php
declare(strict_types=1);

namespace iutnc\deefy\classes;

class PodcastTrack extends AudioTrack
{
    private string $auteur;
    protected int $duration = 0; // cohérent avec AudioTrack

    public function __construct(string $title, string $fileName, string $auteur){
        parent::__construct($title, $fileName);
        $this->auteur = $auteur;
    }

    public function setDuration(int $d): void {
        $this->duration = $d;
    }

    public function __get(string $name): mixed {
        if (!property_exists($this, $name)) {
            throw new \Exception("invalid property : $name");
        }
        return $this->$name;
    }
}
