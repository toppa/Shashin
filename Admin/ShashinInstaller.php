<?php

class Admin_ShashinInstaller {
    private $dbFacade;
    private $album;
    private $photo;
    private $settings;
    private $settingsDefaults = array(
        'divPadding' => 10,
        'thumbPadding' => 6,
        'prefixCaptions' => 'n',
        'scheduledUpdate' => 'n',
        'themeMaxSize' => 600,
        'themeMaxSingle' => 576,
        'photosPerPage' => 18,
        'captionExif' => 'n',
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

    public function __construct(&$dbFacade, &$album, &$photo, &$settings) {
        $this->dbFacade = $dbFacade;
        $this->album = $album;
        $this->photo = $photo;
        $this->settings = $settings;
    }

    public function run() {
        try {
            $this->createAndVerifyTables();
            $this->setDefaultSettings();
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function createAndVerifyTables() {
        $albumTable = $this->album->getTableName();
        $albumRefData = $this->album->getRefData();
        $this->dbFacade->createTable($albumTable, $albumRefData);

        if (!$this->dbFacade->verifyTableExists($albumTable, $albumRefData)) {
            throw new Exception(__('Failed to create table ', 'shashin') . $albumTable);
        }

        $photoTable = $this->photo->getTableName();
        $photoRefData = $this->photo->getRefData();
        $this->dbFacade->createTable($photoTable, $photoRefData);

        if (!$this->dbFacade->verifyTableExists($photoTable, $photoRefData)) {
            throw new Exception(__('Failed to create table ', 'shashin') . $photoTable);
        }

        return true;
    }

    public function setDefaultSettings() {
        return $this->settings->set($this->settingsDefaults);
    }
}