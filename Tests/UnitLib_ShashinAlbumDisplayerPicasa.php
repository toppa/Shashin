<?php

require_once(dirname(__FILE__) . '/../Lib/ShashinDataObject.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinDataObjectDisplayer.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumDisplayerPicasa.php');
Mock::generate('Lib_ShashinAlbum');

class UnitLib_ShashinAlbumDisplayerPicasa extends UnitTestCase {
    private $album;
    private $sampleAlbumData;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->sampleAlbumData = array(
            "id" => "1",
            "sourceId" => "5106043314877888401",
            "albumType" => "picasa",
            "dataUrl" => "http://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5106043314877888401?alt=json",
            "user" => "michaeltoppa",
            "name" => "Michael Toppa",
            "linkUrl" => "https://picasaweb.google.com/michaeltoppa/1999MikeAndMariaSWedding",
            "title" => "1999 - Mike and Maria's Wedding",
            "description" => "",
            "location" => "Stanford, CA",
            "coverPhotoUrl" => "http://lh4.ggpht.com/-b7rVxWSjQak/RtxPV4cN95E/AAAAAAAAIg8/IivNZh6uXV8/s160-c/1999MikeAndMariaSWedding.jpg",
            "lastSync" => "1311646953",
            "photoCount" => "41",
            "pubDate" => "936428400",
            "geoPos" => "37.424106 -122.166076",
            "includeInRandom" => "Y",
            "login" => "",
            "password" => ""
        );

        $this->album = new MockLib_ShashinAlbum();
        $this->album->setReturnValue('__get', $this->sampleAlbumData['coverPhotoUrl'], array('coverPhotoUrl'));
        $this->album->setReturnValue('__get', $this->sampleAlbumData['linkUrl'], array('linkUrl'));
    }

    public function testSetImgSrc() {
        $displayer = new Lib_ShashinAlbumDisplayerPicasa($this->album);
        $displayer->setActualSizeFromValidSizes(150);
        $imgSrc = $displayer->setImgSrc();
        $expectedUrl = 'http://lh4.ggpht.com/-b7rVxWSjQak/RtxPV4cN95E/AAAAAAAAIg8/IivNZh6uXV8/s150-c/1999MikeAndMariaSWedding.jpg';
        $this->assertEqual($expectedUrl, $imgSrc);
    }

    public function testSetAHref() {
        $displayer = new Lib_ShashinAlbumDisplayerPicasa($this->album);
        $aHref = $displayer->setAHref();
        $this->assertEqual($this->sampleAlbumData['linkUrl'], $aHref);
    }
}