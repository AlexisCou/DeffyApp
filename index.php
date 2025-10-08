<?php
session_start();

require_once 'vendor/autoload.php';
require_once 'action/Action.php';
require_once 'action/DefaultAction.php';
require_once 'action/DisplayPlaylistAction.php';
require_once 'action/AddPlaylistAction.php';
require_once 'action/AddPodcastTrackAction.php';
require_once 'classes/AudioList.php';
require_once 'classes/Playlist.php';
require_once 'classes/AudioTrack.php';
require_once 'classes/AlbumTrack.php';
require_once 'classes/PodcastTrack.php';
require_once 'classes/AudioListRenderer.php';
require_once 'dispatch/Dispatcher.php';

use iutnc\deefy\dispatch\Dispatcher;

$dispatcher = new Dispatcher();
$dispatcher->run();
