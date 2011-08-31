<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

class ShashinIntegrationTestsSuite extends TestSuite {
   function __construct() {
       parent::__construct();
       // these integration tests run selects only against the database
       // they assume the existence of an album with album_key = 1
       //$this->addFile('IntegrationLib_ShashinContainer.php');
       //$this->addFile('IntegrationAdmin_ShashinContainer.php');

       // this should not be harmful to run on a existing installation
       $this->addFile('IntegrationAdmin_ShashinInstaller.php');

       // WARNING: running the uninstall integration test will actually uninstall
       // and delete all your Shashin data!
       //$this->addFile('IntegrationShashinUninstaller.php');
   }
}

