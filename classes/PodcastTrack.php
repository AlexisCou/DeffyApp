<?php
declare(strict_types=1);

namespace iutnc\deefy\classes;

class PodcastTrack extends AudioTrack
{
    private string $auteur;
    
    public function __construct(string $title, string $fileName, string $auteur){
        parent::__construct($title, $fileName);
        $this->auteur = $auteur;
    }

    public function __get(string $name): mixed {
    if ($name === 'auteur') {
        return $this->auteur;
    }

        try {
            return parent::__get($name);
        } catch (\Exception $e) {
            throw new \Exception("invalid property : $name");
        }
}
}
