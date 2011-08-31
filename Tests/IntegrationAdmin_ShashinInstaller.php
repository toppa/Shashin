<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');

class IntegrationLib_ShashinInstaller extends UnitTestCase {
    private $installer;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $autoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
        $adminContainer = new Admin_ShashinContainer($autoLoader);
        $this->installer = $adminContainer->getInstaller();
    }

    public function testCreateAlbumTable() {
        $this->assertTrue(is_array($this->installer->createAlbumTable()));
    }

    public function testVerifyAlbumTable() {
        $toppaAutoLoader = new ToppaAutoLoaderWp('/../../toppa-plugin-libraries-for-wordpress');
        $dbFacade = new ToppaDatabaseFacadeWp($toppaAutoLoader);
        $this->installer->setDbFacade($dbFacade);
        $this->assertEqual($this->installer->verifyAlbumTable(), true);
    }

    public function testCreatePhotoTable() {
        $this->assertTrue(is_array($this->installer->createPhotoTable()));
    }

    public function testVerifyPhotoTable() {
        $toppaAutoLoader = new ToppaAutoLoaderWp('/../../toppa-plugin-libraries-for-wordpress');
        $dbFacade = new ToppaDatabaseFacadeWp($toppaAutoLoader);
        $this->installer->setDbFacade($dbFacade);
        $this->assertEqual($this->installer->verifyPhotoTable(), true);
    }

    public function testSetDefaultSettings() {
        $this->assertTrue($this->installer->setDefaultSettings());
    }
}