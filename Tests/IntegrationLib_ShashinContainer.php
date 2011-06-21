<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');

class IntegrationLib_ShashinContainer extends UnitTestCase {
    private $autoLoader;

    public function __construct() {
    }

    public function setUp() {
        $this->autoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
    }

    public function testGetDatabaseFacade() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $dbFacade = $libContainer->getDatabaseFacade();
        $this->assertTrue($dbFacade instanceof ToppaDatabaseFacadeWp);
    }

    public function testGetFunctionsFacade() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $functionsFacade = $libContainer->getFunctionsFacade();
        $this->assertTrue($functionsFacade instanceof ToppaFunctionsFacadeWp);
    }

    public function testGetClonablePhoto() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $photo = $libContainer->getClonablePhoto();
        $this->assertTrue($photo instanceof Lib_ShashinPhoto);
    }

    public function testGetClonableAlbum() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $album = $libContainer->getClonableAlbum();
        $this->assertTrue($album instanceof Lib_ShashinAlbum);
    }

    public function testGetClonableAlbumCollection() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $albumCollection = $libContainer->getClonableAlbumCollection();
        $this->assertTrue($albumCollection instanceof Lib_ShashinAlbumCollection);
    }

    public function testGetSettings() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $settings = $libContainer->getSettings();
        $this->assertTrue($settings instanceof Lib_ShashinSettings);
    }

    public function testGetPhotoDisplayer() {
        $libContainer = new Lib_ShashinContainer($this->autoLoader);
        $album = $libContainer->getClonableAlbum();
        $album->albumType = 'picasa'; // normally we would get this from the album data in the database
        $displayer = $libContainer->getPhotoDisplayer($album);
        $this->assertTrue($displayer instanceof Lib_ShashinPhotoDisplayerPicasa);
    }

    public function testGetPhotoDisplayerWithUndefinedAlbum() {
        try {
            $libContainer = new Lib_ShashinContainer($this->autoLoader);
            $album = $libContainer->getClonableAlbum();
            $displayer = $libContainer->getPhotoDisplayer($album);
            $this->fail("Exception expected - no album type defined");
        }

        catch (Exception $e) {
            $this->pass("received expected exception");
        }
    }
}