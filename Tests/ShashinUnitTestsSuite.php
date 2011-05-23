<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

class ShashinUnitTestsSuite extends TestSuite {
   function __construct() {
       parent::__construct();

       $this->addFile('UnitToppaFunctions.php');
       $this->addFile('UnitShashinPhoto.php');
       $this->addFile('UnitShashinAlbum.php');
       $this->addFile('UnitShashinSettings.php');

/*
       $this->addFile('UnitShashinAlbumRef.php');
       $this->addFile('UnitShashinInstaller.php');
       $this->addFile('UnitShashinShortcodeTransformer.php');
       $this->addFile('UnitShashinShortcodeValidator.php');
       $this->addFile('UnitShashinSynchronizerPicasa.php');

 */
   }
}

