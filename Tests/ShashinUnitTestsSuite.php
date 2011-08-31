<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

class ShashinUnitTestsSuite extends TestSuite {
   function __construct() {
       parent::__construct();
       $this->addFile('UnitAdmin_ShashinInstaller.php');
       $this->addFile('UnitAdmin_ShashinMenuDisplayerAlbums.php');
       $this->addFile('UnitAdmin_ShashinMenuDisplayerPhotos.php');
       $this->addFile('UnitLib_ShashinAlbumCollection.php');
       $this->addFile('UnitPublic_ShashinLayoutManager.php');
       /*
       $this->addFile('UnitLib_ShashinSettings.php');
       $this->addFile('UnitLib_ShashinPhoto.php');
       $this->addFile('UnitLib_ShashinAlbum.php');
       $this->addFile('UnitLib_ShashinPhotoDisplayerPicasaHighslide.php');
       $this->addFile('UnitLib_ShashinAlbumDisplayerPicasa.php');
       $this->addFile('UnitAdmin_ShashinSynchronizerPicasa.php');
       $this->addFile('UnitPublic_ShashinShortcode.php');
      */
   }
}

