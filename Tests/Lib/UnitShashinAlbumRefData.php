<?php

//require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaDatabaseFacadeWp.php');
//require_once(dirname(__FILE__) . '/../Lib/ShashinPhoto.php');
//require_once(dirname(__FILE__) . '/../Lib/UnitShashinAlbumRefData.php');
Mock::generate('ToppaDatabaseFacadeWp');
Mock::generate('Lib_ShashinPhoto');

class UnitShashinAlbum extends UnitTestCase {
/*    private $dbFacade;
    private $clonablePhoto;
    private $sampleAlbumData;
    private $samplePhotoData;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->samplePhotoData = array(
            array(
                'id' => 1,
                'sourceId' => 5590273098322362706,
                'albumId' => 2,
                'filename' => 'IMG_0360.JPG',
                'description' => 'Kai is not so sure about his new friend',
                'linkUrl' => 'https://picasaweb.google.com/michaeltoppa/2011Honolulu',
                'contentUrl' => 'https://lh5.googleusercontent.com/_e1IlgcNcTSg/TZSjw67tQVI/AAAAAAAAIik/LI3EeUEGJYs/IMG_0360.JPG',
                'contentType' => 'image/jpeg',
                'width' => 1024,
                'height' => 768,
                'takenTimestamp' => 1301524665,
                'uploadedTimestamp' => 1301586883,
                'tags' => null,
                'lastSync' => 1304249789,
                'includeInRandom' => 'Y',
                'sourceOrder' => 1,
                'fstop' => 3.2,
                'make' => 'Canon',
                'model' => 'Canon PowerShot SD78',
                'exposure' => 0.0125,
                'focalLength' => 5.9,
                'iso' => 100
            ), array(
                'id' => 2,
                'sourceId' => 5590273098322362707,
                'albumId' => 2,
                'filename' => 'IMG_0361.JPG',
                'description' => '2nd photo',
                'linkUrl' => 'https://picasaweb.google.com/michaeltoppa/2011Honolulu',
                'contentUrl' => 'https://lh5.googleusercontent.com/_e1IlgcNcTSg/TZSjw67tQVI/AAAAAAAAIik/LI3EeUEGJYs/IMG_0361.JPG',
                'contentType' => 'image/jpeg',
                'width' => 1024,
                'height' => 768,
                'takenTimestamp' => 1301524675,
                'uploadedTimestamp' => 1301586893,
                'tags' => null,
                'lastSync' => 1304249789,
                'includeInRandom' => 'Y',
                'sourceOrder' => 2,
                'fstop' => 3.2,
                'make' => 'Canon',
                'model' => 'Canon PowerShot SD78',
                'exposure' => 0.0125,
                'focalLength' => 5.9,
                'iso' => 100
            )
        );

        $this->clonablePhoto = new MockLib_ShashinPhoto();
        $this->clonablePhoto->setReturnValue('set', true);

        $this->sampleAlbumData = array(
            "id" => 2,
            "sourceId" => 5590273039059471873,
            "albumType" => "picasa",
            "dataUrl" => "https://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5590273039059471873?alt=json",
            "user" => "michaeltoppa",
            "name" => "Michael Toppa",
            "linkUrl" => "https://picasaweb.google.com/michaeltoppa/2011Honolulu",
            "filename" => "2011 - Honolulu",
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

        $this->dbFacade = new MockToppaDatabaseFacadeWp();
        $this->dbFacade->setReturnValue('sqlSelectRow', $this->sampleAlbumData);
        $this->dbFacade->setReturnValue('sqlSelectMultipleRows', $this->samplePhotoData);
        $this->dbFacade->setReturnValue('getTableNamePrefix', 'wp_');
        $this->dbFacade->setReturnValue('sqlInsert', 1);
        $this->dbFacade->setReturnValue('sqlDelete', true);
    }

    public function testGetRefData() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $refData = $album->getRefData();
        $this->assertTrue(is_array($refData));
        $this->assertFalse(empty($refData));
    }

    public function testGetTableName() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $this->assertEqual($album->getTableName(), 'wp_shashin_album_3alpha');
    }

    public function testMagicSetAndGetWithValidProperty() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $album->title = 'test title';
        $this->assertEqual($album->title, 'test title');
    }

    public function testMagicGetWithInvalidProperty() {
        try {
            $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
            $album->foobar;
            $this->fail("Exception was expected - invalid __get call");
         }

         catch (Exception $e) {
             $this->pass("received expected exception - invalid __get call");
         }
    }

    public function testMagicSetWithInvalidProperty() {
        try {
            $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
            $album->foobar = 'test foobar';
            $this->fail("Exception was expected - invalid __set call");
         }

         catch (Exception $e) {
             $this->pass("received expected exception - invalid __set call");
         }
    }

    public function testGetAlbum() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $albumData = $album->get(2);
        $this->assertEqual($album->title, $this->sampleAlbumData['title']);
        $this->assertEqual($album->sourceId, $this->sampleAlbumData['sourceId']);
        $this->assertEqual($this->sampleAlbumData, $albumData);
    }

    public function testSetUsingFields() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $album->set($this->sampleAlbumData);
        $albumData = $album->get();
        $this->assertEqual($albumData, $this->sampleAlbumData);
    }

    public function testRefreshAlbumUsingInvalidKey() {
        try {
            $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
            $album->refresh('hello');
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
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $album->set($originalFields);
        $album->set($revisedFields);
        $albumData = $album->getData();
        $this->assertEqual($expectedFinalFields['title'], $album->title);
        $this->assertEqual($expectedFinalFields['name'], $album->name);
        $this->assertEqual($expectedFinalFields, $albumData);
    }

    public function testDeleteAlbum() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
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

    public function testIsVideo() {
        $album = new Lib_ShashinAlbum($this->dbFacade, $this->clonablePhoto);
        $album->get(1);
        $this->assertFalse($album->isVideo());
    }
*/
}