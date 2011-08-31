<?php

class Admin_ShashinInstaller {
    private $dbFacade;
    private $album;
    private $photo;
    private $albumTable;
    private $albumRefData;
    private $photoTable;
    private $photoRefData;

    private $settings;
    private $settingsDefaults = array(
        'thumbPadding' => 6,
        'prefixCaptions' => 'n',
        'scheduledUpdate' => 'n',
        'themeMaxSize' => 600,
        'themeMaxSingle' => 576,
        'photosPerPage' => 18,
        'captionExif' => 'all',
        'imageDisplay' => 'highslide',
        'highslideMax' => 640,
        'highslideVideoWidth' => 640,
        'highslideVideoHeight' => 480,
        'highslideAutoplay' => 'false',
        'highslideInterval' => 5000,
        'highslideOutlineType' => 'rounded-white',
        'highslideDimmingOpacity' => 0.75,
        'highslideRepeat' => '1',
        'highslideVPosition' => 'top',
        'highslideHPosition' => 'center',
        'highslideHideController' => '0',
        'otherRelImage' => null,
        'otherRelVideo' => null,
        'otherRelDelimiter' => null,
        'otherLinkClass' => null,
        'otherLinkTitle' => null,
        'otherImageClass' => null,
        'otherImageTitle' => null,
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
        try {
            $this->createAlbumTable();
            $this->verifyAlbumTable();
            $this->createPhotoTable();
            $this->verifyPhotoTable();
            $this->setDefaultSettings();
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

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

    public function setDefaultSettings() {
        return $this->settings->set($this->settingsDefaults);
    }
}