<?php

require_once(dirname(__FILE__) . '/../Lib/ToppaWpDatabaseFacade.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
Mock::generate('ToppaWpDatabaseFacade');

class UnitShashinInstaller extends UnitTestCase {
    private $dbFacade;
    private $sampleSettings = array(
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


    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->dbFacade = new MockToppaWpDatabaseFacade();
        $this->dbFacade->setReturnValue('setSetting', true);
        $this->dbFacade->setReturnValue('getSetting', $this->sampleSettings);
    }

    public function testGetSettings() {
        $settings = new ShashinSettings($this->dbFacade);
        $settingsData = $settings->get();
        $this->assertEqual($settingsData, $this->sampleSettings);
    }

    public function testAddNewSetting() {
        $testSettingToAdd = array('test' => 'testing');
        $settings = new ShashinSettings($this->dbFacade);
        $settings->set($testSettingToAdd);
        $settingsData = $settings->get();
        $this->assertEqual($settingsData['test'], 'testing');
        $this->assertEqual($settingsData['highslideDimmingOpacity'], 0.75);
    }

    public function testAddInvalidSetting() {
        try {
            $settings = new ShashinSettings($this->dbFacade);
            $settings->set('a string');
            $this->fail("Exception was expected");
         }

         catch (Exception $e) {
             $this->pass("received expected exception");
         }
    }
}