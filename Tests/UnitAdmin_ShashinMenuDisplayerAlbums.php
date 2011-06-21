<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaFunctionsFacadeWp.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumCollection.php');
Mock::generate('ToppaFunctionsFacadeWp');
Mock::generate('Lib_ShashinAlbum');
Mock::generate('Lib_ShashinAlbumCollection');

class UnitAdmin_ShashinMenuDisplayerAlbums extends UnitTestCase {
    private $functionsFacade;
    private $clonableAlbum;
    private $albumCollection;
    private $sampleAlbumData;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->functionsFacade = new MockToppaFunctionsFacadeWp();
        $this->clonableAlbum = new MockLib_ShashinAlbum();
        $this->albumCollection = new MockLib_ShashinAlbumCollection();

        $this->sampleAlbumData = array(
            array(
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
            ),
            array(
                "albumKey" => 3,
                "albumId" => 5590273039059471874,
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

    // not sure how to test checkOrderByNonce(), given the environment setup needed in WP

    public function testSetOrderByClause() {
        $requests['shashinOrderBy'] = 'title';
        $requests['shashinSort'] = 'asc';
        $menuDisplayer = new Admin_ShashinMenuDisplayerAlbums($this->functionsFacade, $requests, $this->clonableAlbum, $this->albumCollection);
        $orderByClause = $menuDisplayer->setOrderByClause();
        $this->assertEqual($orderByClause, "order by title asc");
    }

    public function testSetSortAndOrderByUrlOrderOnTitle() {
        $requests['shashinOrderBy'] = null;
        $requests['shashinSort'] = null;
        $expectedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinSort=desc';
        $menuDisplayer = new Admin_ShashinMenuDisplayerAlbums($this->functionsFacade, $requests, $this->clonableAlbum, $this->albumCollection);
        $url = $menuDisplayer->setSortArrowAndOrderByUrl('title');
        $this->assertEqual($expectedUrl, $url);
        $this->assertEqual('&darr;', $menuDisplayer->getSortArrow());
    }

    public function testSetSortAndOrderByUrlReverseOrderOnTitle() {
        $requests['shashinOrderBy'] = 'title';
        $requests['shashinSort'] = 'asc';
        $expectedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinSort=desc';
        $menuDisplayer = new Admin_ShashinMenuDisplayerAlbums($this->functionsFacade, $requests, $this->clonableAlbum, $this->albumCollection);
        $url = $menuDisplayer->setSortArrowAndOrderByUrl('title');
        $this->assertEqual($expectedUrl, $url);
        $this->assertEqual('&darr;', $menuDisplayer->getSortArrow());
    }
}