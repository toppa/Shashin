<?php

class Admin_ShashinContainer extends Lib_ShashinContainer {
    private $installer;
    private $upgrader;
    private $uninstaller;
    private $menuDisplayerPhotos;
    private $menuActionHandlerPhotos;
    private $menuDisplayerAlbums;
    private $menuActionHandlerAlbums;
    private $settingsMenuManager;
    private $synchronizerPicasa;
    private $synchronizerYoutube;
    private $synchronizerTwitpic;
    private $headTags;
    private $mediaMenu;
    private $scheduledSynchronizer;

    public function __construct($autoLoader) {
        parent::__construct($autoLoader);
    }

    public function getInstaller() {
        if (!$this->installer) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->getClonablePhoto();
            $this->getSettings();
            $this->installer = new Admin_ShashinInstall();
            $this->installer->setDbFacade($this->dbFacade);
            $this->installer->setAlbumAndAlbumVars($this->clonableAlbum);
            $this->installer->setPhotoAndPhotoVars($this->clonablePhoto);
            $this->installer->setSettings($this->settings);
        }

        return $this->installer;
    }

    public function getUpgrader() {
        if (!$this->upgrader) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->getClonablePhoto();
            $this->getFunctionsFacade();
            $this->upgrader = new Admin_ShashinUpgradeWp();
            $this->upgrader->setDbFacade($this->dbFacade);
            $this->upgrader->setAlbum($this->clonableAlbum);
            $this->upgrader->setPhoto($this->clonablePhoto);
            $this->upgrader->setFunctionsFacade($this->functionsFacade);
            $this->upgrader->setAdminContainer($this);
        }

        return $this->upgrader;
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
        $this->menuActionHandlerPhotos = new Admin_ShashinMenuActionHandlerPhotos();
        $this->menuActionHandlerPhotos->setFunctionsFacade($this->functionsFacade);
        $this->menuActionHandlerPhotos->setMenuDisplayer($this->menuDisplayerPhotos);
        $this->menuActionHandlerPhotos->setAdminContainer($this);
        $this->menuActionHandlerPhotos->setRequest($_REQUEST);
        return $this->menuActionHandlerPhotos;
    }

    public function getMenuActionHandlerAlbums() {
        if (!$this->menuActionHandlerAlbums) {
            $this->getFunctionsFacade();
            $this->getMenuDisplayerAlbums();
            $this->getUpgrader();
            $this->menuActionHandlerAlbums = new Admin_ShashinMenuActionHandlerAlbums();
            $this->menuActionHandlerAlbums->setFunctionsFacade($this->functionsFacade);
            $this->menuActionHandlerAlbums->setUpgrader($this->upgrader);
            $this->menuActionHandlerAlbums->setMenuDisplayer($this->menuDisplayerAlbums);
            $this->menuActionHandlerAlbums->setAdminContainer($this);
            $this->menuActionHandlerAlbums->setRequest($_REQUEST);
        }
        return $this->menuActionHandlerAlbums;
    }

    public function getSettingsMenuManager() {
        if (!$this->settingsMenuManager) {
            $this->getFunctionsFacade();
            $this->getSettings();
            $this->settingsMenuManager = new Admin_ShashinSettingsMenu();
            $this->settingsMenuManager->setFunctionsFacade($this->functionsFacade);
            $this->settingsMenuManager->setSettings($this->settings);
            $this->settingsMenuManager->setRequest($_REQUEST);
        }

        return $this->settingsMenuManager;
    }

    public function getSynchronizerPicasa(array $request = null) {
        $this->synchronizerPicasa = new Admin_ShashinSynchronizerPicasa();
        return $this->setupSynchronizer($this->synchronizerPicasa, 'picasa', $request);
    }

    public function getSynchronizerYoutube(array $request = null) {
        $this->synchronizerYoutube = new Admin_ShashinSynchronizerYoutube();
        return $this->setupSynchronizer($this->synchronizerYoutube, 'youtube', $request);
    }

    public function getSynchronizerTwitpic(array $request = null) {
        $this->synchronizerTwitpic = new Admin_ShashinSynchronizerTwitpic();
        return $this->setupSynchronizer($this->synchronizerTwitpic, 'twitpic', $request);
    }

    private function setupSynchronizer($synchronizer, $type, $request = null) {
        if ($request) {
            $synchronizer->setRssUrl($request['rssUrl']);
            $synchronizer->setIncludeInRandom($request['includeInRandom']);
        }

        $this->getFunctionsFacade();
        $httpRequester = $this->functionsFacade->getHttpRequestObject();
        $synchronizer->setHttpRequester($httpRequester);
        $album = $this->getClonableAlbum();
        $album->albumType = $type;
        $synchronizer->setClonableAlbum($album);
        $this->getClonablePhoto();
        $synchronizer->setClonablePhoto($this->clonablePhoto);
        $this->getDatabaseFacade();
        $synchronizer->setDatabaseFacade($this->dbFacade);
        return $synchronizer;
    }

    public function getHeadTags($version) {
        if (!$this->headTags) {
            $this->getFunctionsFacade();
            $this->headTags = new Admin_ShashinHeadTags($version);
            $this->headTags->setFunctionsFacade($this->functionsFacade);
        }
        return $this->headTags;
    }

    public function getMediaMenu($version, array $request) {
        if (!$this->mediaMenu) {
            $publicContainer = new Public_ShashinContainer($this->autoLoader);
            $this->mediaMenu = new Admin_ShashinMediaMenuWp($version);
            $this->mediaMenu->setRequest($request);
            $this->mediaMenu->setContainer($publicContainer);
        }
        return $this->mediaMenu;
    }

    public function getScheduledSynchronizer() {
        $this->scheduledSynchronizer = new Admin_ShashinScheduledSynchronizer();
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $this->getClonableAlbumCollection();
        $this->getMenuActionHandlerAlbums();
        $this->scheduledSynchronizer->setPublicContainer($publicContainer);
        $this->scheduledSynchronizer->setClonableAlbumCollection($this->clonableAlbumCollection);
        $this->scheduledSynchronizer->setAlbumHandler($this->menuActionHandlerAlbums);
        return $this->scheduledSynchronizer;
    }
}
