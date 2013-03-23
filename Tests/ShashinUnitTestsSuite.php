<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

$shashinTestsAutoLoaderPath = dirname(__FILE__) . '/../Lib/ShashinAutoLoader.php';

if (file_exists($shashinTestsAutoLoaderPath)) {
    require_once($shashinTestsAutoLoaderPath);
    $shashinTestsAutoLoader = new ShashinAutoLoader('/shashin');
}

class ShashinUnitTestsSuite extends TestSuite {
   function __construct() {
       parent::__construct();
       $this->addFile('Lib/UnitShashinPhoto.php');
   }
}

