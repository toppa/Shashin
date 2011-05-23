<?php

require_once(dirname(__FILE__) . '/../../toppa-libs/ToppaWpDatabaseFacade.php');
require_once(dirname(__FILE__) . '/../ShashinAlbumRef.php');
require_once(dirname(__FILE__) . '/../ShashinPhotoRef.php');
require_once(dirname(__FILE__) . '/../ShashinSettings.php');
require_once(dirname(__FILE__) . '/../ShashinInstaller.php');

class IntegrationShashinInstaller extends UnitTestCase {
    private $installer;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $dbFacade = new ToppaWpDatabaseFacade();
        $albumRef = new ShashinAlbumRef($dbFacade);
        $photoRef = new ShashinPhotoRef($dbFacade);
        $settings = new ShashinSettings($dbFacade);

        $this->installer = new ShashinInstaller($dbFacade, $albumRef, $photoRef, $settings);
    }

    public function testCreateAndVerifyTables() {
        $this->assertTrue($this->installer->createAndVerifyTables());
    }
}