<?php

class Lib_ShashinContainer {
    protected $autoLoader;
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

    public function __construct($autoLoader) {
        $this->autoLoader = $autoLoader;
    }

    public function getDatabaseFacade() {
        if (!$this->dbFacade) {
            $this->dbFacade = new ToppaDatabaseFacadeWp($this->autoLoader);
        }

        return $this->dbFacade;
    }

    public function getFunctionsFacade() {
        if (!$this->functionsFacade) {
            $this->functionsFacade = new ToppaFunctionsFacadeWp();
        }
        return $this->functionsFacade;
    }

    public function getPhotoRefData() {
        if (!$this->photoRefData) {
            $this->photoRefData = new Lib_ShashinPhotoRefData();
        }

        return $this->photoRefData;
    }

    public function getClonablePhoto() {
        if (!$this->clonablePhoto) {
            $this->getDatabaseFacade();
            $this->getPhotoRefData();
            $this->clonablePhoto = new Lib_ShashinPhoto($this->dbFacade, $this->photoRefData);
        }

        return $this->clonablePhoto;
    }

    public function getClonablePhotoCollection() {
        if (!$this->photoCollection) {
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
        if (!$this->albumPhotosCollection) {
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

    public function getAlbumRefData() {
        if (!$this->albumRefData) {
            $this->albumRefData = new Lib_ShashinAlbumRefData();
        }

        return $this->albumRefData;
    }

    public function getClonableAlbum() {
        if (!$this->clonableAlbum) {
            $this->getDatabaseFacade();
            $this->getAlbumRefData();
            $this->getClonablePhoto();;
            $this->clonableAlbum = new Lib_ShashinAlbum($this->dbFacade, $this->albumRefData, $this->clonablePhoto);
        }

        return $this->clonableAlbum;
    }

    public function getClonableAlbumCollection() {
        if (!$this->clonableAlbumCollection) {
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
        if (!$this->settings) {
            $this->getFunctionsFacade();
            $this->settings = new Lib_ShashinSettings($this->functionsFacade);
        }

        return $this->settings;
    }
}
