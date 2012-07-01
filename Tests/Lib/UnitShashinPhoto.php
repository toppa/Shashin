<?php

Mock::generate('ToppaDatabaseFacadeWp');

class UnitLib_ShashinPhoto extends UnitTestCase {
    private $samplePhotoData;
    private $photo;

    public function __construct() {
        $this->samplePhotoData = array(
            'id' => 1,
            'photoId' => 5590273098322362706,
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
        );
    }

    public function setUp() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('sqlSelectRow', $this->samplePhotoData);
        $dbFacade->setReturnValue('getTableNamePrefix', 'wp_');
        $dbFacade->setReturnValue('sqlInsert', 1);
        $dbFacade->setReturnValue('sqlDelete', true);
        $this->photo = new Lib_ShashinPhoto($dbFacade);
    }

    public function testGetRefData() {
        $refData = $this->photo->getRefData();
        $this->assertTrue(is_array($refData));
        $this->assertFalse(empty($refData));
    }

    public function testGetTableName() {
        $this->assertEqual('wp_shashin_photo', $this->photo->getTableName());
    }

    public function testGetBaseTableName() {
        $this->assertEqual('shashin_photo', $this->photo->getBaseTableName());
    }

    public function testMagicSetAndGetWithValidProperty() {
        $this->photo->filename = 'test filename';
        $this->assertEqual('test filename', $this->photo->filename);
    }

    public function testMagicGetWithInvalidProperty() {
        $this->expectException();
        $this->photo->foobar;
    }

    public function testMagicSetWithInvalidProperty() {
        $this->expectException();
        $this->photo->foobar = 'test foobar';
    }

    public function testGetDataBeforePopulated() {
        $data = $this->photo->getData();
        $this->assertTrue(is_array($data));
        $this->assertTrue(empty($data));
    }

/*

    public function testGetPhoto() {
        $photoData = $photo->get(1);
        $this->assertEqual($photo->filename, $this->samplePhotoData['filename']);
        $this->assertEqual($photo->width, $this->samplePhotoData['width']);
        $this->assertEqual($this->samplePhotoData, $photoData);
    }


    public function testRefreshPhotoUsingInvalidKey() {
        try {
            $photo = new Lib_ShashinPhoto($this->dbFacade);
            $photo->refresh('hello');
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testUpdateOfPhotoFields() {
        $originalFields = array('title' => 'old title', 'description' => 'test description');
        $revisedFields = array('title' => 'new title');
        $expectedFinalFields = array('title' => 'new title', 'description' => 'test description');
        $photo = new Lib_ShashinPhoto($this->dbFacade);
        $photo->set($originalFields);
        $photo->set($revisedFields);
        $photoData = $photo->getData();
        $this->assertEqual($expectedFinalFields['filename'], $photo->filename);
        $this->assertEqual($expectedFinalFields['description'], $photo->description);
        $this->assertEqual($expectedFinalFields, $photoData);
    }

    public function testDeletePhoto() {
        $photo = new Lib_ShashinPhoto($this->dbFacade);
        $photo->get(1);
        $photoFilename = $photo->filename;
        $photoData = $photo->delete();
        $this->assertEqual($photoData['filename'], $photoFilename);

        try {
            $photo->filename;
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testIsVideo() {
        $photo = new Lib_ShashinPhoto($this->dbFacade);
        $photo->get(1);
        $this->assertFalse($photo->isVideo());
    } */
}
