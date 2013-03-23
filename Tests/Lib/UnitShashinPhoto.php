<?php

Mock::generate('Lib_ShashinDatabaseFacade');

class UnitLib_ShashinPhoto extends UnitTestCase {
    private $samplePhotoData = array(
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
    );
    private $photo;

    public function __construct() {
    }

    public function setUp() {
        $dbFacade = new MockShashinDatabaseFacade();
        // the sample data has an ID of 1, so only return the data if the ID requested is 1
        $dbFacade->setReturnValue('sqlSelectRow', $this->samplePhotoData, array('*', '*', array('id' => 1)));
        $dbFacade->setReturnValue('getTableNamePrefix', 'wp_');
        $dbFacade->setReturnValue('sqlInsert', 1);
        $dbFacade->setReturnValue('sqlDelete', true);
        $dbFacade->setReturnValue('getIntTypes', array(
                'tinyint', 'smallint', 'mediumint', 'int', 'bigint',
                'tinyint unsigned', 'smallint unsigned', 'mediumint unsigned', 'int unsigned', 'bigint unsigned'
            )
        );
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

    public function testMagicSetAndGetWithValidValue() {
        $this->photo->filename = 'test filename';
        $this->assertEqual('test filename', $this->photo->filename);
    }

    public function testMagicSetAndGetWithUnsetValue() {
        $this->assertNull($this->photo->filename);
    }

    public function testMagicGetWithInvalidProperty() {
        $this->expectException();
        $this->photo->foobar;
    }

    public function testMagicSetWithInvalidProperty() {
        $this->expectException();
        $this->photo->foobar = 'test foobar';
    }

    public function testRefreshWithNoArgument() {
        $this->expectError(); // for the missing argument
        $this->expectException(); // for the explicitly thrown exception
        $this->photo->refresh();
    }

    public function testRefreshWithInvalidIdDataType() {
        $this->expectException();
        $this->photo->refresh('foo');
    }

    public function testRefreshWithValidId() {
        $photoData = $this->photo->refresh(1);
        $this->assertTrue(is_array($photoData));
        $this->assertEqual(1, $photoData['id']);
    }

    public function testRefreshWithPhotoNotFound() {
        $this->expectException();
        $this->photo->refresh(2);
    }

    // this is essentially the same as testing refresh()
    public function testGet() {
        $photoData = $this->photo->refresh(1);
        $this->assertTrue(is_array($photoData));
        $this->assertEqual(1, $photoData['id']);
    }

    public function testSetWithNoArgument() {
        $this->expectError(); // for the type hinting fail
        $this->expectException(); // for the argument not being set
        $this->photo->set();
    }

    public function testSetWithEmptyArray() {
        $this->expectException();
        $this->photo->set(array());
    }

    public function testSetWithValidData() {
        $this->assertEqual($this->samplePhotoData, $this->photo->set($this->samplePhotoData));
    }

    public function testSetWithNonNumericAlbumId() {
        $badData = $this->samplePhotoData;
        $badData['albumId'] = 'foo';
        $this->expectException();
        $this->photo->set($badData);
    }

    public function testFlush() {
        $this->assertTrue($this->photo->flush());
        $this->assertEqual(1, $this->photo->id);
    }

    public function testDelete() {
        $this->photo->set($this->samplePhotoData);
        // the deleted data is returned
        $this->assertEqual($this->samplePhotoData, $this->photo->delete());
    }
/*
 * @todo: old tests - need to clean up



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
