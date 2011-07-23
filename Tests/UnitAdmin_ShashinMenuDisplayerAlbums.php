<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaFunctionsFacadeWp.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumCollection.php');
Mock::generate('ToppaFunctionsFacadeWp');
Mock::generate('Lib_ShashinAlbum');
Mock::generate('Lib_ShashinAlbumCollection');

class UnitAdmin_ShashinMenuDisplayerAlbums extends UnitTestCase {
    private $functionsFacade;
    private $albumCollection;
    private $sampleAlbumData;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->functionsFacade = new MockToppaFunctionsFacadeWp();
        $this->albumCollection = new MockLib_ShashinAlbumCollection();

        $this->sampleAlbumData = array(
            array(
                "id" => 2,
                "sourceId" => 5590273039059471873,
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
            ),
            array(
                "id" => 3,
                "sourceId" => 5590273039059471874,
                "albumType" => "picasa",
                "dataUrl" => "https://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5590273039059471874?alt=json",
                "user" => "michaeltoppa",
                "name" => "Michael Toppa",
                "linkUrl" => "https://picasaweb.google.com/michaeltoppa/2012Honolulu",
                "title" => "2012 - Honolulu",
                "description" => "",
                "location" => "",
                "coverPhotoUrl" => "https://lh6.googleusercontent.com/_e1IlgcNcTSg/TZSjteKVTgE/AAAAAAAAIos/CpFDUjMK_K4/s160-c/2012Honolulu.jpg",
                "lastSync" => 1302478486,
                "photoCount" => 28,
                "pubDate" => 1301539065,
                "geoPos" => "",
                "includeInRandom" => "Y",
                "login" => null,
                "password" => null
            )
        );
    }

    public function testSetShortcodeMimic() {
        $menuDisplayer = new Admin_ShashinMenuDisplayerAlbums($this->functionsFacade, array(), $this->albumCollection);
        $shortcodeMimic = $menuDisplayer->setShortcodeMimic('title', 'y');
        $this->assertEqual($shortcodeMimic, array('order' => 'title', 'reverse' => 'y'));
    }

    public function testSetSortAndOrderByUrlOrderOnTitle() {
        $requests['shashinOrderBy'] = null;
        $requests['shashinReverse'] = null;
        $expectedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinReverse=y';
        $menuDisplayer = new Admin_ShashinMenuDisplayerAlbums($this->functionsFacade, $requests, $this->albumCollection);
        $url = $menuDisplayer->setSortArrowAndOrderByUrl('title');
        $this->assertEqual($expectedUrl, $url);
        $this->assertEqual('&darr;', $menuDisplayer->getSortArrow());
    }

    public function testSetSortAndOrderByUrlReverseOrderOnTitle() {
        $requests['shashinOrderBy'] = 'title';
        $requests['shashinReverse'] = 'y';
        $expectedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinReverse=n';
        $menuDisplayer = new Admin_ShashinMenuDisplayerAlbums($this->functionsFacade, $requests, $this->albumCollection);
        $url = $menuDisplayer->setSortArrowAndOrderByUrl('title');
        $this->assertEqual($expectedUrl, $url);
        $this->assertEqual('&uarr;', $menuDisplayer->getSortArrow());
    }
}