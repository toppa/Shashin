<?php

require_once(dirname(__FILE__) . '/../facade/ToppaWpFunctionsFacade.php');
require_once(dirname(__FILE__) . '/../facade/ToppaWpDatabaseFacade.php');
require_once(dirname(__FILE__) . '/../album/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../ShashinToolsMenu.php');
Mock::generate('ToppaWpFunctionsFacade');
Mock::generate('ToppaWpDatabaseFacade');
Mock::generate('ShashinAlbumRefData');

class UnitTestOfShashinToolsMenu extends UnitTestCase {
    private $toolsMenu;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $functionsFacade = new MockToppaWpFunctionsFacade();
        $dbFacade = new MockToppaWpDatabaseFacade();
        $albumRef = new MockShashinAlbumRefData($dbFacade);
        $this->toolsMenu = new ShashinMenuDisplayerAlbums($functionsFacade, $albumRef);
    }

    public function testDerivePicasaUrlForJsonFeedWithAlbumRssUrl() {
        $picasaAlbumRssUrl = 'https://picasaweb.google.com/data/feed/base/user/michaeltoppa?alt=rss&kind=album&hl=en_US';
        $picasaAlbumJsonUrl = 'https://picasaweb.google.com/data/feed/api/user/michaeltoppa?alt=json&kind=album&hl=en_US';
        $this->assertEqual($this->toolsMenu->derivePicasaUrlForJsonFeed($picasaAlbumRssUrl), $picasaAlbumJsonUrl);
    }

    public function testDerivePicasaUrlForJsonFeedWithUserRssUrl() {
        $picasaUserRssUrl = 'https://picasaweb.google.com/data/feed/base/user/michaeltoppa/albumid/5488529329622136673?alt=rss&kind=photo&hl=en_US';
        $picasaUserJsonUrl = 'https://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5488529329622136673?alt=json&kind=photo&hl=en_US';
        $this->assertEqual($this->toolsMenu->derivePicasaUrlForJsonFeed($picasaUserRssUrl), $picasaUserJsonUrl);
    }

}