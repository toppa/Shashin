<?php

require_once(dirname(__FILE__) . '/../Public/ShashinLayoutManager.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaFunctionsFacadeWp.php');
require_once(dirname(__FILE__) . '/../Public/ShashinContainer.php');
require_once(dirname(__FILE__) . '/../Public/ShashinShortcode.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumPhotosCollection.php');
Mock::generate('Lib_ShashinSettings');
Mock::generate('ToppaFunctionsFacadeWp');
Mock::generate('Public_ShashinContainer');
Mock::generate('Public_ShashinShortcode');
Mock::generate('Lib_ShashinAlbumPhotosCollection');

class UnitPublic_ShashinLayoutManager extends UnitTestCase {
    private $layoutManager;

    public function setUp() {
        $this->layoutManager = new Public_ShashinLayoutManager();
    }

/*
    public function testSetSettings() {
    }

    public function testSetFunctionsFacade() {
    }

    public function testSetContainer() {
    }

    public function testSetShortcode() {
    }

    public function testSetDataObjectCollection() {
    }
*/
    public function testSetRequest() {
        $request = array('shashinPage' => 3);
        $this->assertEqual($request, $this->layoutManager->setRequest($request));
    }
/*
    public function testRun() {
    }

    public function testSetThumbnailCollectionIfNeeded() {
    }

    public function testSetCollection() {
    }

    public function testInitializeSessionGroupCounter() {
    }

    public function testSetOpeningTableTag() {
    }

    public function testAddStyleForOpeningTableTag() {
    }

*/
    public function testSetTableCaptionTagWithNoPagination() {
        $collection = new MockLib_ShashinAlbumPhotosCollection();
        $collection->setReturnValue('getMayNeedPagination', false);
        $this->layoutManager->setDataObjectCollection($collection);
        $this->assertNull($this->layoutManager->setTableCaptionTag());
    }

    public function testSetTableCaptionTagWithFewerPhotosThanMaxPerPage() {
        $collection = new MockLib_ShashinAlbumPhotosCollection();
        $collection->setReturnValue('getMayNeedPagination', true);
        $collection->setReturnValue('getCount', 5);
        $this->layoutManager->setDataObjectCollection($collection);
        $settings = new MockLib_ShashinSettings();
        $settings->setReturnValue('__get',10, 'photosPerPage');
        $this->layoutManager->setSettings($settings);
        $this->assertNull($this->layoutManager->setTableCaptionTag());
    }

    public function testSetTableCaptionTagWithNextPagination() {
        $collection = new MockLib_ShashinAlbumPhotosCollection();
        $collection->setReturnValue('getMayNeedPagination', true);
        $collection->setReturnValue('getCount', 11);
        $this->layoutManager->setDataObjectCollection($collection);
        $settings = new MockLib_ShashinSettings();
        $settings->setReturnValue('__get',10, 'photosPerPage');
        $this->layoutManager->setSettings($settings);
        $this->assertEqual('<caption> Next1</caption>' . PHP_EOL, $this->layoutManager->setTableCaptionTag());
    }
/*
    public function testSetTableBody() {
    }

    public function testSetGroupCounterHtml() {
    }

    public function setCombinedTags() {
    }

    public function incrementSessionGroupCounter() {
    }
*/
}
