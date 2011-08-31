<?php
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaDatabaseFacadeWp.php');
require_once(dirname(__FILE__) . '/../Admin/ShashinInstaller.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinDataObject.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhoto.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
Mock::generate('ToppaDatabaseFacadeWp');
Mock::generate('Lib_ShashinAlbum');
Mock::generate('Lib_ShashinPhoto');
Mock::generate('Lib_ShashinSettings');

class UnitAdmin_ShashinInstaller extends UnitTestCase {
    private $installer;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->installer = new Admin_ShashinInstaller();
    }

    public function testSetDbFacade() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $result = $this->installer->setDbFacade($dbFacade);
        $this->assertEqual($dbFacade, $result);
    }

    public function testSetAlbumAndAlbumVars() {
        $album = new MockLib_ShashinAlbum();
        $result = $this->installer->setAlbumAndAlbumVars($album);
        $this->assertEqual($album, $result);
    }

    public function testSetPhotoAndPhotoVars() {
        $photo = new MockLib_ShashinPhoto();
        $result = $this->installer->setPhotoAndPhotoVars($photo);
        $this->assertEqual($photo, $result);
    }

    public function testSetSettings() {
        $settings = new MockLib_ShashinSettings();
        $result = $this->installer->setSettings($settings);
        $this->assertEqual($settings, $result);
    }

    private function albumSetup() {
        require('dataFiles/albumRefData.php');
        $album = new MockLib_ShashinAlbum();
        $album->setReturnValue('getTableName', 'wp_shashin_album');
        $album->setReturnValue('getRefData', $albumRefData);
        //$album->setReturnValue('getRefData', array());
        $this->installer->setAlbumAndAlbumVars($album);
    }

    public function testCreateAlbumTable() {
        //typical return value from createTable
        $createTableResult = array(
          "wp_shashin_album_3alpha.id" =>
            "Changed type of wp_shashin_album_3alpha.id from smallint(5) unsigned to smallint unsigned",
          "wp_shashin_album_3alpha.lastSync" =>
            "Changed type of wp_shashin_album_3alpha.lastSync from int(10) unsigned to int unsigned",
          "wp_shashin_album_3alpha.photoCount" =>
            "Changed type of wp_shashin_album_3alpha.photoCount from smallint(5) unsigned to smallint unsigned",
          "wp_shashin_album_3alpha.pubDate" =>
            "Changed type of wp_shashin_album_3alpha.pubDate from int(10) unsigned to int unsigned"
        );

        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('createTable', $createTableResult);
        $this->installer->setDbFacade($dbFacade);
        $this->albumSetup();
        $this->assertEqual($this->installer->createAlbumTable(), $createTableResult);
    }

    public function testVerifyAlbumTableIfExists() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('verifyTableExists', true);
        $this->installer->setDbFacade($dbFacade);
        $this->albumSetup();
        $this->assertEqual($this->installer->verifyAlbumTable(), true);
    }

    public function testVerifyAlbumTableIfNotExists() {
        try {
            $dbFacade = new MockToppaDatabaseFacadeWp();
            $dbFacade->setReturnValue('verifyTableExists', false);
            $this->installer->setDbFacade($dbFacade);
            $this->albumSetup();
            $this->installer->verifyAlbumTable();
            $this->fail("Exception was expected");
         }

         catch (Exception $e) {
             $this->pass("received expected exception");
         }
    }

    private function photoSetup() {
        require('dataFiles/photoRefData.php');
        $photo = new MockLib_ShashinPhoto();
        $photo->setReturnValue('getTableName', 'wp_shashin_photo');
        $photo->setReturnValue('getRefData', $photoRefData);
        $this->installer->setPhotoAndPhotoVars($photo);
    }


    public function testCreatePhotoTable() {
        //typical return value from createTable
        $createTableResult = array(
            "wp_shashin_photo_3alpha.id" =>
                "Changed type of wp_shashin_photo_3alpha.id from int(10) unsigned to int unsigned",
            "wp_shashin_photo_3alpha.albumId" =>
                "Changed type of wp_shashin_photo_3alpha.albumId from smallint(5) unsigned to smallint unsigned",
            "wp_shashin_photo_3alpha.width" =>
                "Changed type of wp_shashin_photo_3alpha.width from smallint(5) unsigned to smallint unsigned"
        );

        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('createTable', $createTableResult);
        $this->installer->setDbFacade($dbFacade);
        $this->photoSetup();
        $this->assertEqual($this->installer->createPhotoTable(), $createTableResult);

    }

    public function testVerifyPhotoTableIfExists() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('verifyTableExists', true);
        $this->installer->setDbFacade($dbFacade);
        $this->photoSetup();
        $this->assertEqual($this->installer->verifyPhotoTable(), true);
    }

    public function testVerifyPhotoTableIfNotExists() {
        try {
            $dbFacade = new MockToppaDatabaseFacadeWp();
            $dbFacade->setReturnValue('verifyPhotoTable', false);
            $this->installer->setDbFacade($dbFacade);
            $this->photoSetup();
            $this->installer->verifyPhotoTable();
            $this->fail("Exception was expected");
         }

         catch (Exception $e) {
             $this->pass("received expected exception");
         }
    }

    public function testSetDefaultSettings() {
        $settings = new MockLib_ShashinSettings();
        $settings->setReturnValue('set', array());
        $this->installer->setSettings($settings);
        $this->assertEqual($this->installer->setDefaultSettings, array());
    }
}