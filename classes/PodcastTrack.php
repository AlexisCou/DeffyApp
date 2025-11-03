<?php
declare(strict_types=1);

namespace iutnc\deefy\classes;

class PodcastTrack extends AudioTrack
{
    protected string $auteur;

    public function __construct(string $title, string $fileName, string $auteur){
        parent::__construct($title, $fileName);
        $this->auteur = $auteur;
    }

    public function setDuration(int $d): void {
        $this->duration = $d;
    }

    public function getDuration(): int {
        return $this->duration;
    }

    public function __get(string $name): mixed {
        try {
            return parent::__get($name);
        } catch (\Exception $e) {
            if (property_exists($this, $name)) {
                return $this->$name;
            }
            throw new \Exception("invalid property : $name");
        }
    }
    

}
