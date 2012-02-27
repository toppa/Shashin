<?php

class Admin_ShashinInstall {
    private $dbFacade;
    private $album;
    private $photo;
    private $functionsFacade;
    private $settings;
    private $settingsDefaults = array(
        'supportOldShortcodes' => 'n',
        'imageDisplay' => 'fancybox',
        'expandedImageSize' => 'medium',
        'defaultPhotoLimit' => 18,
        'scheduledUpdate' => 'n',
        'captionExif' => 'all',
        'thumbPadding' => 6,
        'themeMaxSize' => 600,
        'albumPhotosSize' => 'small',
        'albumPhotosCrop' => 'y',
        'albumPhotosColumns' => '3',
        'albumPhotosOrder' => 'source',
        'albumPhotosOrderReverse' => 'n',
        'albumPhotosCaption' => 'n',
        'highslideAutoplay' => 'false',
        'highslideInterval' => 5000,
        'highslideRepeat' => '1',
        'highslideOutlineType' => 'rounded-white',
        'highslideDimmingOpacity' => 0.75,
        'highslideHideController' => '0',
        'highslideVPosition' => 'top',
        'highslideHPosition' => 'center',
        'otherRelImage' => null,
        'otherRelVideo' => null,
        'otherRelDelimiter' => null,
        'otherLinkClass' => null,
        'otherImageClass' => null,
        'otherTitle' => array(),
    );

    public function __construct() {
    }

    public function setDbFacade(ToppaDatabaseFacade $dbFacade) {
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

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function run() {
        return $this->functionsFacade->callFunctionForNetworkSites(array($this, 'runForNetworkSites'));
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
        return $this->settings->set($this->settingsDefaults, true);
    }
}
