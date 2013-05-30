<?php

class Admin_ShashinInstall {
    private $dbFacade;
    private $album;
    private $photo;
    private $functionsFacade;
    private $version;
    private $settings;
    private $settingsDefaults = array(
        'version' => null,
        'supportOldShortcodes' => 'n',
        'imageDisplay' => 'prettyphoto',
        'expandedImageSize' => 'small',
        'defaultPhotoLimit' => 18,
        'scheduledUpdate' => 'n',
        'captionExif' => 'all',
        'themeMaxSize' => 600,
        'albumPhotosSize' => 'small',
        'albumPhotosCrop' => 'y',
        'albumPhotosColumns' => '3',
        'albumPhotosOrder' => 'source',
        'albumPhotosOrderReverse' => 'n',
        'albumPhotosCaption' => 'n',
        'prettyPhotoTheme' => 'pp_default',
        'prettyPhotoOverlayGallery' => '1',
        'prettyPhotoShowTitle' => '1',
        'prettyPhotoShowSocialButtons' => '1',
        'prettyPhotoAutoplaySlideshow' => '0',
        'prettyPhotoSlideshow' => '5000',
        'prettyPhotoLoadScript' => 'y',
        'fancyboxCyclic' => '0',
        'fancyboxVideoWidth' => '560',
        'fancyboxVideoHeight' => '340',
        'fancyboxTransition' => 'fade',
        'fancyboxInterval' => null,
        'fancyboxLoadScript' => 'y',
        'otherRelImage' => null,
        'otherRelVideo' => null,
        'otherRelDelimiter' => null,
        'otherLinkClass' => null,
        'otherImageClass' => null,
        'otherTitle' => array(),
        'externalViewers' => array()
    );

    public function __construct($version) {
        $this->version = $version;
    }

    public function setDbFacade(Lib_ShashinDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
        return $this->dbFacade;
    }

    public function setAlbum(Lib_ShashinAlbum $album) {
        $this->album = $album;
        return $this->album;
    }

    public function setPhoto(Lib_ShashinPhoto $photo) {
        $this->photo = $photo;
        return $this->photo;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function run() {
        return $this->functionsFacade->callFunctionForNetworkSites(array($this, 'runForNetworkSites'));
    }

    public function runtimeUpgrade() {
        $status = shashinActivationChecks();

        if (is_string($status)) {
            shashinCancelActivation($status);
            return null;
        }

        // update the version number if needed
        $allSettings = $this->settings->refresh();

        //if (!isset($allSettings['version']) || version_compare($allSettings['version'], $this->version, '<')) {
            $this->updateSettings();
        //}

        return true;
    }

    public function runForNetworkSites() {
        // this is called for each site in the network, so the table
        // name prefix will be different for each call
        $albumTable = $this->dbFacade->getTableNamePrefix() . $this->album->getBaseTableName();
        $photoTable = $this->dbFacade->getTableNamePrefix() . $this->photo->getBaseTableName();
        $this->createAlbumTable($albumTable);
        $this->verifyAlbumTable($albumTable);
        $this->createPhotoTable($photoTable);
        $this->verifyPhotoTable($photoTable);
        $this->updateSettings();
        return true;
    }

    public function createAlbumTable($albumTable) {
        return $this->dbFacade->createTable($albumTable, $this->album->getRefData());
    }

    public function verifyAlbumTable($albumTable) {
        $result = $this->dbFacade->verifyTableExists($albumTable, $this->album->getRefData());

        if (!$result) {
            throw new Exception(__('Failed to create table ', 'shashin') . $albumTable);
        }

        return $result;
    }

    public function createPhotoTable($photoTable) {
        return $this->dbFacade->createTable($photoTable, $this->photo->getRefData());
    }

    public function verifyPhotoTable($photoTable) {
        $result = $this->dbFacade->verifyTableExists($photoTable, $this->photo->getRefData());

        if (!$result) {
            throw new Exception(__('Failed to create table ', 'shashin') . $photoTable);
        }

        return $result;
    }

    public function updateSettings() {
        $this->settings->refresh();
        $this->settings->set(array('version' => $this->version));
        return $this->settings->set($this->settingsDefaults, true);
    }
}
