<?php

class IntegrationShashinWp extends UnitTestCase {
    private $shashin;

    public function __construct() {
    }

    public function setUp() {
        $autoLoaderPath = dirname(__FILE__) . '/../Lib/ShashinAutoLoader.php';
        require_once($autoLoaderPath);
        new ShashinAutoLoader('/shashin');
        $this->shashin = new ShashinWp();
    }

    public function testDeactivateHighslideWithHighslideActive() {
        // in the context of the test, the deactive_plugins method isn't available yet, so include its file
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $libContainer = new Lib_ShashinContainer();
        $settings = $libContainer->getSettings();
        $originalDisplay = $settings->imageDisplay;
        $settings->set(array('imageDisplay' => 'highslide'));
        $result = $this->shashin->deactivateHighslide();
        $this->assertEqual($result, 'prettyphoto');
        $settings->set(array('imageDisplay', $originalDisplay));
    }

    public function testDeactivateHighslideWithHighslideInactive() {
        $libContainer = new Lib_ShashinContainer();
        $settings = $libContainer->getSettings();
        $originalDisplay = $settings->imageDisplay;
        $settings->set(array('imageDisplay' => 'prettyphoto'));
        $result = $this->shashin->deactivateHighslide();
        $this->assertEqual($result, true);
        $settings->set(array('imageDisplay', $originalDisplay));
    }
}
