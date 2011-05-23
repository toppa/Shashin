<?php

require_once(dirname(__FILE__) . '/../Lib/ToppaWpDatabaseFacade.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhoto.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
Mock::generate('ToppaWpDatabaseFacade');
Mock::generate('Shashin3AlphaPhoto');

class UnitShashinAlbum extends UnitTestCase {
    private $dbFacade;
    private $clonablePhoto;
    private $sampleAlbumData;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->dbFacade = new MockToppaWpDatabaseFacade();
        $this->clonablePhoto = new MockShashin3AlphaPhoto();
        $this->sampleAlbumData = array(
            "albumKey" => 2,
            "albumId" => 5590273039059471873,
            "albumType" => "picasa",
            "dataUrl" => "https://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5590273039059471873?alt=json",
            "user" => "michaeltoppa",
            "name" => "Michael Toppa",
            "linkUrl" => "https://picasaweb.google.com/michaeltoppa/2011Honolulu",
            "title" => "2011 - Honolulu",
            "description" => "",
            "location" => "",
            "coverPhotoUrl" => "https://lh6.googleusercontent.com/_e1IlgcNcTSg/TZSjteKVTgE/AAAAAAAAIos/CpFDUjMK_K4/s160-c/2011Honolulu.jpg",
            "lastSync" => 1302478486,
            "photoCount" => 28,
            "pubDate" => 1301539065,
            "geoPos" => "",
            "includeInRandom" => "Y",
            "login" => null,
            "password" => null
        );

        $this->dbFacade = new MockToppaWpDatabaseFacade();
        $this->dbFacade->setReturnValue('sqlSelectRow', $this->sampleAlbumData);
        $this->dbFacade->setReturnValue('getTableNamePrefix', 'wp_');
        $this->dbFacade->setReturnValue('sqlInsert', 1);
        $this->dbFacade->setReturnValue('sqlDelete', true);
    }

    public function testGetRefData() {
        $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
        $refData = $album->getRefData();
        $this->assertTrue(is_array($refData));
        $this->assertFalse(empty($refData));
    }

    public function testGetTableName() {
        $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
        $this->assertEqual($album->getTableName(), 'wp_shashin_album_3alpha');
    }

    public function testMagicSetAndGetWithValidProperty() {
        $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
        $album->title = 'test title';
        $this->assertEqual($album->title, 'test title');
    }

    public function testMagicGetWithInvalidProperty() {
        try {
            $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
            $album->foobar;
            $this->fail("Exception was expected - invalid __get call");
         }

         catch (Exception $e) {
             $this->pass("received expected exception - invalid __get call");
         }
    }

    public function testMagicSetWithInvalidProperty() {
        try {
            $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
            $album->foobar = 'test foobar';
            $this->fail("Exception was expected - invalid __set call");
         }

         catch (Exception $e) {
             $this->pass("received expected exception - invalid __set call");
         }
    }


    public function testGetAlbum() {
        $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
        $albumData = $album->get(2);
        $this->assertEqual($album->title, $this->sampleAlbumData['title']);
        $this->assertEqual($album->albumId, $this->sampleAlbumData['albumId']);
        $this->assertEqual($this->sampleAlbumData, $albumData);
    }

    public function testGetAlbumUsingInvalidKey() {
        try {
            $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
            $album->get('hello');
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testUpdateOfAlbumFields() {
        $originalFields = array('title' => 'old title', 'name' => 'test name');
        $revisedFields = array('title' => 'new title');
        $expectedFinalFields = array('title' => 'new title', 'name' => 'test name');
        $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
        $album->set($originalFields);
        $album->set($revisedFields);
        $albumData = $album->getData();
        $this->assertEqual($expectedFinalFields['title'], $album->title);
        $this->assertEqual($expectedFinalFields['name'], $album->name);
        $this->assertEqual($expectedFinalFields, $albumData);
    }

    public function testDeleteAlbum() {
        $album = new Shashin3AlphaAlbum($this->dbFacade, $this->clonablePhoto);
        $album->get(2);
        $albumTitle = $album->title;
        $albumData = $album->delete();
        $this->assertEqual($albumData['title'], $albumTitle);

        try {
            $album->title;
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }
}