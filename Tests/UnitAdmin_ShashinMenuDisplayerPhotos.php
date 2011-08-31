<?php
require_once(dirname(__FILE__) . '/../Admin/ShashinMenuDisplayer.php');
require_once(dirname(__FILE__) . '/../Admin/ShashinMenuDisplayerPhotos.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
Mock::generate('Lib_ShashinAlbum');

class UnitAdmin_ShashinMenuDisplayerPhotos extends UnitTestCase {
    private $menuDisplayer;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->menuDisplayer = new Admin_ShashinMenuDisplayerPhotos();
    }

    // parent class tests are in UnitAdmin_ShashinMenuDisplayerAlbums
    public function testSetAlbum() {
        $album = new MockLib_ShashinAlbum();
        $result = $this->menuDisplayer->setAlbum($album);
        $this->assertEqual($album, $result);
    }

    public function testGenerateOrderByLink() {
        $noncedUrl = '?page=Shashin3AlphaToolsMenu&shashinOrderBy=source&shashinReverse=y&shashinMenu=photos&id=50&_wpnonce=669d485aa7';
        $expectedLink = "<a href=\"$noncedUrl\">Source Order &darr;</a>";
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('addNonceToUrl', $noncedUrl);
        $this->menuDisplayer = new Admin_ShashinMenuDisplayerPhotos();
        $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $link = $this->menuDisplayer->generateOrderByLink('source', 'Source Order');
        $this->assertEqual($expectedLink, $link);
    }
}
