<?php

class Admin_ShashinContainer extends Lib_ShashinContainer {
    private $installer;
    private $uninstaller;
    private $menuDisplayerPhotos;
    private $menuActionHandlerPhotos;
    private $menuDisplayerAlbums;
    private $menuActionHandlerAlbums;
    private $settingsDisplayer;
    private $synchronizerPicasa;
    private $headTagsBuilder;

    public function __construct(&$autoLoader) {
        parent::__construct($autoLoader);
    }

    public function getInstaller() {
        if (!$this->installer) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->getClonablePhoto();
            $this->getSettings();
            $this->installer = new Admin_ShashinInstaller();
            $this->installer->setDbFacade($this->dbFacade);
            $this->installer->setAlbumAndAlbumVars($this->clonableAlbum);
            $this->installer->setPhotoAndPhotoVars($this->clonablePhoto);
            $this->installer->setSettings($this->settings);
        }

        return $this->installer;
    }

    public function getUninstaller() {
        if (!$this->uninstaller) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->getClonablePhoto();
            $this->getSettings();
            $this->uninstaller = new Admin_ShashinUninstaller($this->dbFacade, $this->clonableAlbum, $this->clonablePhoto, $this->settings);
        }
        return $this->uninstaller;
    }

    public function getMenuDisplayerPhotos($albumKey) {
        $this->getFunctionsFacade();
        $this->getClonablePhotoCollection();
        $album = $this->getClonableAlbum();
        $album->get($albumKey);
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $this->menuDisplayerPhotos = new Admin_ShashinMenuDisplayerPhotos();
        $this->menuDisplayerPhotos->setFunctionsFacade($this->functionsFacade);
        $this->menuDisplayerPhotos->setRequest($_REQUEST);
        $this->menuDisplayerPhotos->setCollection($this->clonablePhotoCollection);
        $this->menuDisplayerPhotos->setContainer($publicContainer);
        $this->menuDisplayerPhotos->setAlbum($album);
        return $this->menuDisplayerPhotos;
    }

    public function getMenuDisplayerAlbums() {
        if (!$this->menuDisplayerAlbums) {
            $this->getFunctionsFacade();
            $this->getClonableAlbumCollection();
            $publicContainer = new Public_ShashinContainer($this->autoLoader);
            $this->menuDisplayerAlbums = new Admin_ShashinMenuDisplayerAlbums();
            $this->menuDisplayerAlbums->setFunctionsFacade($this->functionsFacade);
            $this->menuDisplayerAlbums->setRequest($_REQUEST);
            $this->menuDisplayerAlbums->setCollection($this->clonableAlbumCollection);
            $this->menuDisplayerAlbums->setContainer($publicContainer);
        }

        return $this->menuDisplayerAlbums;
    }

    public function getMenuActionHandlerPhotos($albumKey) {
        $this->getFunctionsFacade();
        $this->getMenuDisplayerPhotos($albumKey);
        $this->menuActionHandlerPhotos = new Admin_ShashinMenuActionHandlerPhotos(
            $this->functionsFacade,
            $this->menuDisplayerPhotos,
            $this,
            $_REQUEST);
        return $this->menuActionHandlerPhotos;
    }

    public function getMenuActionHandlerAlbums() {
        if (!$this->menuActionHandlerAlbums) {
            $this->getFunctionsFacade();
            $this->getMenuDisplayerAlbums();
            $this->menuActionHandlerAlbums = new Admin_ShashinMenuActionHandlerAlbums(
                $this->functionsFacade,
                $this->menuDisplayerAlbums,
                $this,
                $_REQUEST);
        }
        return $this->menuActionHandlerAlbums;
    }

    public function getSettingsDisplayer() {
        if (!$this->settingsDisplayer) {
            $this->getFunctionsFacade();
            $this->settingsDisplayer = new Admin_ShashinSettingsDisplayer($this->functionsFacade);
        }

        return $this->settingsDisplayer;
    }

    public function getSynchronizerPicasa(array $request = null) {
        $this->synchronizerPicasa = new Admin_ShashinSynchronizerPicasa();

        if ($request) {
            $this->synchronizerPicasa->setRssUrl($request['rssUrl']);
            $this->synchronizerPicasa->setIncludeInRandom($request['includeInRandom']);
        }

        $this->getFunctionsFacade();
        $httpRequester = $this->functionsFacade->getHttpRequestObject();
        $this->synchronizerPicasa->setHttpRequester($httpRequester);

        $album = $this->getClonableAlbum();
        $album->albumType = 'picasa';
        $this->synchronizerPicasa->setClonableAlbum($album);

        $photo = $this->getClonablePhoto();
        $this->synchronizerPicasa->setClonablePhoto($photo);

        return $this->synchronizerPicasa;
    }

    public function getDocHeadUrlsFetcher() {
        if (!$this->headTagsBuilder) {
            $this->getFunctionsFacade();
            $this->headTagsBuilder = new Admin_ShashinDocHeadUrlsFetcher($this->functionsFacade);
        }
        return $this->headTagsBuilder;
    }
}
