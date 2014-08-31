<?php

###
# @author		cternes
# @copyright		2014 by cternes
# @version		1.0.0
# @description		This plugin generates an RSS feed out of your latest public photos
###

# Config
if (file_exists('config.ini')) {
    $config = parse_ini_file('config.ini');
}
else {
    exit('Error: config.ini not found');
}

# Include
require($config['lychee'] . 'php/define.php');
require($config['lychee'] . 'php/autoload.php');
require($config['lychee'] . 'data/config.php');
require('RssGenerator.php');
require('DataProvider.php');
require('vendor/autoload.php');

# Define
defineTablePrefix($dbTablePrefix);

# Set Mime Type
header('Content-type: application/rss+xml');

$rssGenerator = new RssGenerator();
$dataProvider = new DataProvider($dbHost, $dbUser, $dbPassword, $dbName);
# If a album name is provided, we'll create a feed only for this album
if(!empty($_GET['album'])) {
    $albums = $dataProvider->getPublicAlbums();
    $albumId = getAlbumIdByName($albums, $_GET['album']);
    
    if(empty($albumId)) {
	die('Could not find an album with title: ' .$_GET['album']);
    }
    
    $photos = $dataProvider->getPhotosByAlbum($albumId);
    # Generate RSS
    echo $rssGenerator->buildRssFeedForAlbum($_GET['album'], $photos);
}
# If no album name is provided, we'll create a feed with all public photos
else {
    # Get latest photos
    $photos = $dataProvider->getPhotostream();

    # Generate RSS
    echo $rssGenerator->buildRssFeedLatestPhotos($photos);
}

function getAlbumIdByName($albums, $name) {
    foreach ($albums['content'] as $album) {
	if(strtolower($album['title']) === strtolower($name)) {
	    return $album['id'];
	}
    }
}

?>