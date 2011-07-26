<?php

class Lib_ShashinContainer {
    protected $autoLoader;
    protected $dbFacade;
    protected $functionsFacade;
    protected $shortcodeValidator;
    protected $clonablePhoto;
    protected $clonablePhotoCollection;
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

    public function getClonablePhoto() {
        if (!$this->clonablePhoto) {
            $this->getDatabaseFacade();
            $this->clonablePhoto = new Lib_ShashinPhoto($this->dbFacade);
        }

        return $this->clonablePhoto;
    }

    public function getClonablePhotoCollection() {
        if (!$this->photoCollection) {
            $this->getDatabaseFacade();
            $this->getClonablePhoto();
            $this->clonablePhotoCollection = new Lib_ShashinPhotoCollection($this->dbFacade, $this->clonablePhoto);
        }

        return $this->clonablePhotoCollection;
    }

    public function getClonableAlbum() {
        if (!$this->clonableAlbum) {
            $this->getDatabaseFacade();
            $this->getClonablePhoto();;
            $this->clonableAlbum = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        }

        return $this->clonableAlbum;
    }

    public function getClonableAlbumCollection() {
        if (!$this->clonableAlbumCollection) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->clonableAlbumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);
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

    public function getPhotoDisplayer(Lib_ShashinPhoto $photo, Lib_ShashinPhoto $alternativeThumbnail = null) {
        switch ($photo->albumType) {
            case 'picasa':
                $photoDisplayer = new Lib_ShashinPhotoDisplayerPicasa($photo, $alternativeThumbnail);
                break;
        }

        return $photoDisplayer;
    }
}
