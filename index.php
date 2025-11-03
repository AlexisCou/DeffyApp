<?php

require_once 'vendor/autoload.php';
session_start();

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

DeefyRepository::setConfig('db.config.ini');
$repo = DeefyRepository::getInstance();
$repo->initializeAudioTables();

$dispatcher = new Dispatcher();
$dispatcher->run();