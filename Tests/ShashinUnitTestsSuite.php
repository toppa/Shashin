<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

$shashinTestsAutoLoaderPath = dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

if (file_exists($shashinTestsAutoLoaderPath)) {
    require_once($shashinTestsAutoLoaderPath);
    $shashinTestsToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $shashinTestsAutoLoader = new ToppaAutoLoaderWp('/shashin');
}

class ShashinUnitTestsSuite extends TestSuite {
   function __construct() {
       parent::__construct();
       $this->addFile('Lib/UnitShashinAlbumRefData.php');
       $this->addFile('Lib/UnitShashinPhotoRefData.php');
   }
}

