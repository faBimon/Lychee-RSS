<?php

class DataProvider
{
    private $database = null;
    private $settings = null;
    
    public function __construct($dbHost, $dbUser, $dbPassword, $dbName) {
	# Connect
	$this->database = Database::connect($dbHost, $dbUser, $dbPassword, $dbName);
	
	# Load settings
	$settings = new Settings($this->database);
	$this->settings = $settings->get();
    }
    
    public function getPhotostream() {
	    # Get latests photos
	    $query = Database::prepare($this->database, 'SELECT p.id as photoId, p.title, p.description, a.id as albumId FROM ? p LEFT OUTER JOIN ? a ON p.album = a.id WHERE a.public = 1 ORDER BY p.id DESC LIMIT 50', array(LYCHEE_TABLE_PHOTOS, LYCHEE_TABLE_ALBUMS));
	    $photos = $this->database->query($query);
	    if (!photos) {
		Log::error($this->database, __METHOD__, __LINE__, $this->database->error);
		die('Error: Could not fetch the latest photos from the database.');
	    }
	    return $photos;
    }
    
    public function getPublicAlbums() {
	$album = new Album($this->database, null, $this->settings, null);
	return $album->getAll(true);
	#print var_dump($album->getAll(true)['content'][1]);
    }
}

?>
