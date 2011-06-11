<?php

require_once(dirname(__FILE__) . '/../../toppa-libs/ToppaAutoLoaderWp.php');

class IntegrationShashinInstaller extends UnitTestCase {
    private $installer;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $autoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
        $adminContainer = new Admin_ShashinContainer($autoLoader);
        $this->installer = $adminContainer->getInstaller();
    }

    public function testCreateAndVerifyTables() {
        $this->assertTrue($this->installer->createAndVerifyTables());
    }

    public function testSetDefaultSettings() {
        $this->assertTrue($this->installer->setDefaultSettings());
    }
}