<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');

class IntegrationShashinUninstaller extends UnitTestCase {
    private $uninstaller;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $autoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
        $adminContainer = new Admin_ShashinContainer($autoLoader);
        $this->uninstaller = $adminContainer->getUninstaller();
    }

    public function testDropTables() {
        $this->assertTrue($this->uninstaller->dropTables());
    }

    public function testDeleteSettings() {
        $this->assertTrue($this->uninstaller->deleteSettings());
    }
}