<?php

require_once('Photo/ShashinPhotoRef.php');
require_once('Photo/ShashinPhoto.php');
require_once('Photo/ShashinPhotoSet.php');
require_once('Photo/ShashinPhotoDisplayerPicasa.php');
require_once('Album/ShashinAlbumRef.php');
require_once('Album/ShashinAlbum.php');
require_once('Album/ShashinAlbumSet.php');
require_once('Synchronizer/ShashinSynchronizerPicasa.php');

class ShashinObjectsSetup {
    private $dbFacade;
    private $functionsFacade;

    public function __construct(&$dbFacade, &$functionsFacade) {
        $this->dbFacade = $dbFacade;
        $this->functionsFacade = $functionsFacade;
    }

    public function setupPhotoRef() {
        return new ShashinPhotoRef($this->dbFacade);
    }

    public function setupPhoto() {
        $photoRef = $this->setupPhotoRef();
        return new Shashin3AlphaPhoto($this->dbFacade, $photoRef);
    }

    public function setupAlbumRef() {
        return new ShashinAlbumRef($this->dbFacade);
    }

    public function setupAlbum($albumKey = null) {
        $albumRef = $this->setupAlbumRef();
        $clonablePhoto = $this->setupPhoto();
        $album = new Shashin3AlphaAlbum($this->dbFacade, $albumRef, $clonablePhoto);

        if ($albumKey) {
            $album->getAlbum($albumKey);
        }

        return $album;
    }

    public function setupAlbumSet() {
        $clonableAlbum = $this->setupAlbum();
        return new ShashinAlbumSet($this->dbFacade, $clonableAlbum);
    }

    public function setupSynchronizerPicasa($requests = null) {
        $synchronizer = new ShashinSynchronizerPicasa();

        if ($requests) {
            $synchronizer->setRssUrl($requests['rssUrl']);
            $synchronizer->setIncludeInRandom($requests['includeInRandom']);
        }

        $httpRequester = $this->functionsFacade->getHttpRequestObject();
        $synchronizer->setHttpRequester($httpRequester);

        $album = $this->setupAlbum();
        $album->albumType = 'picasa';
        $synchronizer->setClonableAlbum($album);

        $albumRef = $this->setupAlbumRef();
        $synchronizer->setAlbumRef($albumRef);

        $photo = $this->setupPhoto();
        $synchronizer->setClonablePhoto($photo);

        $photoRef = $this->setupPhotoRef();
        $synchronizer->setPhotoRef($photoRef);

        return $synchronizer;
    }

    public function setupPhotoDisplayer(&$album) {
        switch ($album->albumType) {
            case 'picasa':
                $photoDisplayer = new ShashinPhotoDisplayerPicasa($album);
        }

        return $photoDisplayer;
    }

    public function setupPhotoSet() {
        $photoRef = $this->setupPhotoRef();
        $clonablePhoto = $this->setupPhoto();
        $albumRef = $this->setupAlbumRef();
        $clonableAlbum = $this->setupAlbum();
        return new ShashinPhotoSet($this->dbFacade, $clonablePhoto, $photoRef, $clonableAlbum, $albumRef);
    }
}
