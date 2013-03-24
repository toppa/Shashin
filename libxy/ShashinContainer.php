<?php

class Lib_ShashinContainer {
    protected $dbFacade;
    protected $functionsFacade;
    protected $photoRefData;
    protected $clonablePhoto;
    protected $clonablePhotoCollection;
    protected $clonableAlbumPhotosCollection;
    protected $albumRefData;
    protected $clonableAlbum;
    protected $clonableAlbumCollection;
    protected $photoCollection;
    protected $settings;
    protected $photoDisplayer;

    public function __construct() {
    }

    public function getDatabaseFacade() {
        if (!isset($this->dbFacade)) {
            $this->dbFacade = new Lib_ShashinDatabaseFacade();
        }

        return $this->dbFacade;
    }

    public function getFunctionsFacade() {
        if (!isset($this->functionsFacade)) {
            $this->functionsFacade = new Lib_ShashinFunctionsFacade();
        }
        return $this->functionsFacade;
    }

    public function getClonablePhoto() {
        if (!isset($this->clonablePhoto)) {
            $this->getDatabaseFacade();
            $this->clonablePhoto = new Lib_ShashinPhoto($this->dbFacade);
        }

        return $this->clonablePhoto;
    }

    public function getClonablePhotoCollection() {
        if (!isset($this->photoCollection)) {
            $this->getDatabaseFacade();
            $this->getClonablePhoto();
            $this->getSettings();
            $this->clonablePhotoCollection = new Lib_ShashinPhotoCollection();
            $this->clonablePhotoCollection->setDbFacade($this->dbFacade);
            $this->clonablePhotoCollection->setClonableDataObject($this->clonablePhoto);
            $this->clonablePhotoCollection->setSettings($this->settings);
        }

        return $this->clonablePhotoCollection;
    }

    public function getClonableAlbumPhotosCollection() {
        if (!isset($this->albumPhotosCollection)) {
            $this->getDatabaseFacade();
            $this->getClonablePhoto();
            $this->getSettings();
            $this->clonableAlbumPhotosCollection = new Lib_ShashinAlbumPhotosCollection();
            $this->clonableAlbumPhotosCollection->setDbFacade($this->dbFacade);
            $this->clonableAlbumPhotosCollection->setClonableDataObject($this->clonablePhoto);
            $this->clonableAlbumPhotosCollection->setSettings($this->settings);
        }

        return $this->clonableAlbumPhotosCollection;
    }

    public function getClonableAlbum() {
        if (!isset($this->clonableAlbum)) {
            $this->getDatabaseFacade();
            $this->getClonablePhoto();;
            $this->clonableAlbum = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        }

        return $this->clonableAlbum;
    }

    public function getClonableAlbumCollection() {
        if (!isset($this->clonableAlbumCollection)) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->getSettings();
            $this->clonableAlbumCollection = new Lib_ShashinAlbumCollection();
            $this->clonableAlbumCollection->setDbFacade($this->dbFacade);
            $this->clonableAlbumCollection->setClonableDataObject($this->clonableAlbum);
            $this->clonableAlbumCollection->setSettings($this->settings);
        }

        return $this->clonableAlbumCollection;
    }

    public function getSettings() {
        if (!isset($this->settings)) {
            $this->getFunctionsFacade();
            $this->settings = new Lib_ShashinSettings($this->functionsFacade);
        }

        return $this->settings;
    }
}
