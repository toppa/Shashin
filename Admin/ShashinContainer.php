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
    private $synchronizer;
    private $headTags;
    private $mediaMenu;
    private $scheduledSynchronizer;

    public function __construct() {
        parent::__construct();
    }

    public function getInstaller($version) {
        if (!$this->installer) {
            $this->getDatabaseFacade();
            $this->getClonableAlbum();
            $this->getClonablePhoto();
            $this->getSettings();
            $this->getFunctionsFacade();
            $this->installer = new Admin_ShashinInstall($version);
            $this->installer->setDbFacade($this->dbFacade);
            $this->installer->setAlbum($this->clonableAlbum);
            $this->installer->setPhoto($this->clonablePhoto);
            $this->installer->setSettings($this->settings);
            $this->installer->setFunctionsFacade($this->functionsFacade);
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
            $this->getFunctionsFacade();
            $this->uninstaller = new Admin_ShashinUninstaller();
            $this->uninstaller->setDbFacade($this->dbFacade);
            $this->uninstaller->setAlbum($this->clonableAlbum);
            $this->uninstaller->setPhoto($this->clonablePhoto);
            $this->uninstaller->setSettings($this->settings);
            $this->uninstaller->setFunctionsFacade($this->functionsFacade);
        }

        return $this->uninstaller;
    }

    public function getMenuDisplayerPhotos($albumKey) {
        $this->getFunctionsFacade();
        $this->getClonablePhotoCollection();
        $album = $this->getClonableAlbum();
        $album->get($albumKey);
        $publicContainer = new Public_ShashinContainer();
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
            $publicContainer = new Public_ShashinContainer();
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

    public function getSynchronizer(array $request) {
        if (!in_array($request['shashinAlbumType'], array('picasa', 'youtube', 'twitpic'))) {
            throw New Exception(__('Invalid album type: ', 'shashin') . htmlentities($request['shashinAlbumType']));
        }

        $classToCall = 'Admin_ShashinSynchronizer' . ucfirst($request['shashinAlbumType']);
        $this->synchronizer = new $classToCall();
        $this->synchronizer->setRequest($request);
        $this->getFunctionsFacade();
        $httpRequester = $this->functionsFacade->getHttpRequestObject();
        $this->synchronizer->setHttpRequester($httpRequester);
        $album = $this->getClonableAlbum();
        $album->albumType = $request['shashinAlbumType'];
        $this->synchronizer->setClonableAlbum($album);
        $this->getClonablePhoto();
        $this->synchronizer->setClonablePhoto($this->clonablePhoto);
        $this->getDatabaseFacade();
        $this->synchronizer->setDatabaseFacade($this->dbFacade);
        return $this->synchronizer;
    }

    public function getHeadTags($version) {
        if (!$this->headTags) {
            $this->getFunctionsFacade();
            $this->headTags = new Admin_ShashinHeadTags($version);
            $scriptsObject = $this->functionsFacade->getScriptsObject();
            $this->headTags->setFunctionsFacade($this->functionsFacade);
            $this->headTags->setScriptsObject($scriptsObject);
        }
        return $this->headTags;
    }

    public function getMediaMenu($version, array $request) {
        if (!$this->mediaMenu) {
            $publicContainer = new Public_ShashinContainer();
            $this->mediaMenu = new Admin_ShashinMediaMenuWp($version);
            $this->mediaMenu->setRequest($request);
            $this->mediaMenu->setContainer($publicContainer);
        }
        return $this->mediaMenu;
    }

    public function getScheduledSynchronizer() {
        $this->scheduledSynchronizer = new Admin_ShashinScheduledSynchronizer();
        $publicContainer = new Public_ShashinContainer();
        $this->getClonableAlbumCollection();
        $this->getMenuActionHandlerAlbums();
        $this->scheduledSynchronizer->setPublicContainer($publicContainer);
        $this->scheduledSynchronizer->setClonableAlbumCollection($this->clonableAlbumCollection);
        $this->scheduledSynchronizer->setAlbumHandler($this->menuActionHandlerAlbums);
        return $this->scheduledSynchronizer;
    }

    public static function getDataObjectCollection(array $shortcodeData) {
        $publicContainer = new Public_ShashinContainer();
        $shortcode = $publicContainer->getShortcode($shortcodeData);
        $methodToCall = 'getClonable' . ucfirst($shortcodeData['type']) . 'Collection';
        $collection = $publicContainer->$methodToCall();
        $collection->setNoLimit(true);
        return $collection->getCollectionForShortcode($shortcode);
    }
}
