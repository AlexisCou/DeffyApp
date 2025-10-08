<?php
require_once 'vendor/autoload.php';
session_start();

use iutnc\deefy\dispatch\Dispatcher;

$dispatcher = new Dispatcher();
$dispatcher->run();
