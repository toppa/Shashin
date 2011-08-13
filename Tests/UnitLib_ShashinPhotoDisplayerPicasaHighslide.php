<?php

require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinDataObject.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhoto.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinDataObjectDisplayer.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhotoDisplayerPicasa.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhotoDisplayerPicasaHighslide.php');
Mock::generate('Lib_ShashinSettings');
Mock::generate('Lib_ShashinPhoto');

class UnitLib_ShashinPhotoDisplayerPicasaHighslide extends UnitTestCase {
    private $samplePhotoData;
    private $sampleSettingsData;
    private $displayer;

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
        $this->photo->setReturnValue('__get', $this->samplePhotoData['width'], array('width'));
        $this->photo->setReturnValue('__get', $this->samplePhotoData['height'], array('height'));

        $this->sampleSettingsData = array(
            'divPadding' => 10,
            'thumbPadding' => 6,
            'prefixCaptions' => "n",
            'scheduledUpdate' => "n",
            'themeMaxSize' => 600,
            'themeMaxSingle' => 576,
            'photosPerPage' => 18,
            'captionExif' => "n",
            'imageDisplay' => "highslide",
            'highslideMax' => 640,
            'highslideVideoWidth' => 640,
            'highslideVideoHeight' => 480,
            'highslideAutoplay' => "false",
            'highslideInterval' => 5000,
            'highslideOutlineType' => "rounded-white",
            'highslideDimmingOpacity' => 0.75,
            'highslideRepeat' => "1",
            'highslideVPosition' => "top",
            'highslideHPosition' => "center",
            'highslideHideController' => "0",
            'otherRelImage' => NULL,
            'otherRelVideo' => NULL,
            'otherRelDelimiter' => NULL,
            'otherLinkClass' => NULL,
            'otherLinkTitle' => NULL,
            'otherImageClass' => NULL,
            'otherImageTitle' => NULL
        );

        $this->displayer = new Lib_ShashinPhotoDisplayerPicasaHighslide($this->sampleSettingsData, $this->photo);
    }

    public function testSetNumericSizeFromRequestedSizeWithInvalidSize() {
        try {
            $numericSize = $this->displayer->setNumericSizeFromRequestedSize('xyz');
            $this->fail('Exception expected - invalid requested size');
        }

        catch (Exception $e) {
            $this->pass('received expected exception');
        }
    }

    public function testSetNumericSizeFromRequestedSizeWithStringSize() {
        $numericSize = $this->displayer->setNumericSizeFromRequestedSize('xsmall');
        $this->assertEqual(72, $numericSize);
    }

    public function testSetNumericSizeFromRequestedSizeWithNumericSize() {
        $numericSize = $this->displayer->setNumericSizeFromRequestedSize(162);
        $this->assertEqual(162, $numericSize);
    }

    public function testSetActualSizeFromValidSizesWithLowerThanValidSize() {
        $actualSize = $this->displayer->setActualSizeFromValidSizes(199);
        $this->assertEqual(200, $actualSize);
    }

    public function testSetActualSizeFromValidSizesWithValidSize() {
        $actualSize = $this->displayer->setActualSizeFromValidSizes(200);
        $this->assertEqual(200, $actualSize);
    }

    public function testSetActualSizeFromValidSizesWithGreaterThanValidSize() {
        $actualSize = $this->displayer->setActualSizeFromValidSizes(201);
        $this->assertEqual(220, $actualSize);
    }

    public function testSetDisplayCroppedIfRequestedWithInvalidCropSize() {
        $this->displayer->setActualSizeFromValidSizes(200);
        $shouldBeFalse = $this->displayer->setDisplayCropped('y');
        $this->assertFalse($shouldBeFalse);
    }

    public function testSetDisplayCroppedIfRequestedWithValidCropSize() {
        $this->displayer->setActualSizeFromValidSizes(160);
        $shouldBeTrue = $this->displayer->setDisplayCropped('y');
        $this->assertTrue($shouldBeTrue);
    }

    public function testSetImgWidthAndHeightWithGreaterWidth() {
        $this->displayer->setActualSizeFromValidSizes(200);
        $actualWidthAndHeight = $this->displayer->setImgWidthAndHeight();
        $expectedWidthAndHeight = array(200, 150);
        $this->assertEqual($expectedWidthAndHeight, $actualWidthAndHeight);
    }

    public function testSetImgWidthAndHeightWithCrop() {
        $this->displayer->setActualSizeFromValidSizes(160);
        $this->displayer->setDisplayCropped('y');
        $actualWidthAndHeight = $this->displayer->setImgWidthAndHeight();
        $expectedWidthAndHeight = array(160, 160);
        $this->assertEqual($expectedWidthAndHeight, $actualWidthAndHeight);
    }

    public function testSessionCounterAndSetLinkId() {
        $this->displayer->initializeSessionIdCounter();
        $this->assertEqual(1, $_SESSION['shashin_id_counter']);
        $linkId = $this->displayer->setLinkId();
        $this->assertEqual('shashin_thumb_link_1', $linkId);
        $this->displayer->incrementSessionIdCounter();
        $this->assertEqual(2, $_SESSION['shashin_id_counter']);
    }

    public function testSetImgSrcWithValidCrop() {
        $this->displayer->setActualSizeFromValidSizes(160);
        $this->displayer->setDisplayCropped('y');
        $expectedSrc = "https://lh5.googleusercontent.com/_e1IlgcNcTSg/TZSjw67tQVI/AAAAAAAAIik/LI3EeUEGJYs/IMG_0360.JPG?imgmax=160&amp;crop=1";
        $actualSrc = $this->displayer->setImgSrc();
        $this->assertEqual($expectedSrc, $actualSrc);
    }

    public function testSetLinkHref() {
        $linkHref = $this->displayer->setLinkHref();
        $expectedHref = "https://lh5.googleusercontent.com/_e1IlgcNcTSg/TZSjw67tQVI/AAAAAAAAIik/LI3EeUEGJYs/IMG_0360.JPG?imgmax=640";
        $this->assertEqual($expectedHref, $linkHref);
    }

    public function testSetLinkHrefVideo() {
        $linkHref = $this->displayer->setLinkHrefVideo();
        $expectedHref = 'http://video.google.com/googleplayer.swf?videoUrl=https%3A%2F%2Flh5.googleusercontent.com%2F_e1IlgcNcTSg%2FTZSjw67tQVI%2FAAAAAAAAIik%2FLI3EeUEGJYs%2FIMG_0360.JPG&amp;autoPlay=true';
        $this->assertEqual($expectedHref, $linkHref);
    }

    public function testSetLinkOnClick() {
        $onClick = $this->displayer->setLinkOnClick();
        $this->assertEqual('return hs.expand(this)', $onClick);
    }

    public function testSetLinkOnClickVideoI() {
        $onClick = $this->displayer->setLinkOnClickVideo();
        $expectedOnClick = "return hs.htmlExpand(this,{ objectType:'swf', minWidth: 660, minHeight: 500, objectWidth: 640, objectHeight: 480, allowSizeReduction: false, preserveContent: false";
        $this->assertEqual($expectedOnClick, $onClick);
    }

    public function testSetLinkClass() {
        $class = $this->displayer->setLinkClass();
        $this->assertEqual('highslide', $class);
    }
}
