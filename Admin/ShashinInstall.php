<?php

class Admin_ShashinInstall {
    private $dbFacade;
    private $album;
    private $photo;
    private $albumTable;
    private $albumRefData;
    private $photoTable;
    private $photoRefData;
    private $settings;
    private $settingsDefaults = array(
        'supportOldShortcodes' => 'n',
        'imageDisplay' => 'highslide',
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

    public function setAlbumAndAlbumVars(Lib_ShashinAlbum $album) {
        $this->album = $album;
        $this->albumTable = $this->album->getTableName();
        $this->albumRefData = $this->album->getRefData();
        return $this->album;
    }

    public function setPhotoAndPhotoVars(Lib_ShashinPhoto $photo) {
        $this->photo = $photo;
        $this->photoTable = $this->photo->getTableName();
        $this->photoRefData = $this->photo->getRefData();
        return $this->photo;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function run() {
        $this->createAlbumTable();
        $this->verifyAlbumTable();
        $this->createPhotoTable();
        $this->verifyPhotoTable();
        $this->updateSettings();
        return true;
    }

    public function createAlbumTable() {
        return $this->dbFacade->createTable($this->albumTable, $this->albumRefData);
    }

    public function verifyAlbumTable() {
        $result = $this->dbFacade->verifyTableExists($this->albumTable, $this->albumRefData);

        if (!$result) {
            throw new Exception(__('Failed to create table ', 'shashin') . $this->albumTable);
        }

        return $result;
    }

    public function createPhotoTable() {
        return $this->dbFacade->createTable($this->photoTable, $this->photoRefData);
    }

    public function verifyPhotoTable() {
        $result = $this->dbFacade->verifyTableExists($this->photoTable, $this->photoRefData);

        if (!$result) {
            throw new Exception(__('Failed to create table ', 'shashin') . $this->photoTable);
        }

        return $result;
    }

    public function updateSettings() {
        return $this->settings->set($this->settingsDefaults, true);
    }
}