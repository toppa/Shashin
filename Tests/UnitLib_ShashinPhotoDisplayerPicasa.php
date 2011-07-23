<?php

require_once(dirname(__FILE__) . '/../Lib/ShashinDataObject.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhoto.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhotoDisplayer.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhotoDisplayerPicasa.php');
Mock::generate('Lib_ShashinPhoto');

class UnitLib_ShashinPhotoDisplayerPicasa extends UnitTestCase {
    private $photo;
    private $samplePhotoData;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->samplePhotoData = array(
            'id' => 1,
            'photoId' => 5590273098322362706,
            'albumId' => 2,
            'filename' => 'IMG_0360.JPG',
            'description' => 'Kai is not so sure about his new friend',
            'linkUrl' => 'https://picasaweb.google.com/michaeltoppa/2011Honolulu#5590273098322362706',
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

        $this->photo = new MockLib_ShashinPhoto();
        $this->photo->setReturnValue('__get', $this->samplePhotoData['contentUrl'], array('contentUrl'));
        $this->photo->setReturnValue('__get', $this->samplePhotoData['linkUrl'], array('linkUrl'));
    }

    public function testSetNumericSizeFromRequestedSize() {
        $displayer = new Lib_ShashinPhotoDisplayerPicasa($this->photo);
        $numericSize = $displayer->setNumericSizeFromRequestedSize('xsmall');
        $this->assertEqual(72, $numericSize);
        $numericSize = $displayer->setNumericSizeFromRequestedSize(162);
        $this->assertEqual(162, $numericSize);

        try {
            $numericSize = $displayer->setNumericSizeFromRequestedSize('xyz');
            $this->fail('Exception expected - invalid requested size');
        }

        catch (Exception $e) {
            $this->pass('received expected exception');
        }
    }

    public function testSetActualSizeFromValidSizes() {
        $displayer = new Lib_ShashinPhotoDisplayerPicasa($this->photo);
        $actualSize = $displayer->setActualSizeFromValidSizes(199);
        $this->assertEqual(200, $actualSize);
        $actualSize = $displayer->setActualSizeFromValidSizes(200);
        $this->assertEqual(200, $actualSize);
        $actualSize = $displayer->setActualSizeFromValidSizes(201);
        $this->assertEqual(288, $actualSize);
    }

    public function testSetDisplayCroppedIfRequested() {
        $displayer = new Lib_ShashinPhotoDisplayerPicasa($this->photo);
        $displayer->setActualSizeFromValidSizes(200);
        $shouldBeFalse = $displayer->setDisplayCroppedIfRequested(true);
        $this->assertFalse($shouldBeFalse);
        $displayer->setActualSizeFromValidSizes(160);
        $shouldBeTrue = $displayer->setDisplayCroppedIfRequested(true);
        $this->assertTrue($shouldBeTrue);
    }

    public function testSetAHref() {
        $displayer = new Lib_ShashinPhotoDisplayerPicasa($this->photo);
        $aHref = $displayer->setAHref();
        $this->assertEqual($this->samplePhotoData['linkUrl'], $aHref);
    }

    public function testSetAId() {
        $displayer = new Lib_ShashinPhotoDisplayerPicasa($this->photo);
        $aId = $displayer->setAId();
        $this->assertEqual('shashin_thumb_link_1', $aId);
    }

    public function testIncrementSessionIdCounter() {
        $displayer = new Lib_ShashinPhotoDisplayerPicasa($this->photo);
        $displayer->incrementSessionIdCounter();
        $this->assertEqual(2, $_SESSION['shashin_id_counter']);
    }
}