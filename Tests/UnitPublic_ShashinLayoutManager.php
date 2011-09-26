<?php

require_once(dirname(__FILE__) . '/../Public/ShashinLayoutManager.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaFunctionsFacadeWp.php');
require_once(dirname(__FILE__) . '/../Public/ShashinContainer.php');
require_once(dirname(__FILE__) . '/../Public/ShashinShortcode.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumPhotosCollection.php');
require_once(dirname(__FILE__) . '/../Public/ShashinSessionManager.php');
Mock::generate('Lib_ShashinSettings');
Mock::generate('ToppaFunctionsFacadeWp');
Mock::generate('Public_ShashinContainer');
Mock::generate('Public_ShashinShortcode');
Mock::generate('Lib_ShashinAlbumPhotosCollection');
Mock::generate('Public_ShashinSessionManager');

class UnitPublic_ShashinLayoutManager extends UnitTestCase {
    private $layoutManager;

    public function setUp() {
        $this->layoutManager = new Public_ShashinLayoutManager();
    }

    public function testSetSettings() {
        $settings = new MockLib_ShashinSettings();
        $this->assertEqual($settings, $this->layoutManager->setSettings($settings));
    }


    public function testSetFunctionsFacade() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $this->assertEqual($functionsFacade, $this->layoutManager->setFunctionsFacade($functionsFacade));
    }

    public function testSetContainer() {
        $container = new MockPublic_ShashinContainer();
        $this->assertEqual($container, $this->layoutManager->setContainer($container));
    }


    public function testSetShortcode() {
        $shortcode = new MockPublic_ShashinShortcode();
        $this->assertEqual($shortcode, $this->layoutManager->setShortcode($shortcode));
    }

    public function testSetDataObjectCollection() {
        $dataObjectCollection = new MockLib_ShashinAlbumPhotosCollection();
        $this->assertEqual($dataObjectCollection, $this->layoutManager->setDataObjectCollection($dataObjectCollection));
    }

    public function testSetRequest() {
        $request = array('shashinParentAlbumTitle' => 'Various');
        $this->assertEqual($request, $this->layoutManager->setRequest($request));
    }

    public function testSetSessionManager() {
        $sessionManager = new MockPublic_ShashinSessionManager();
        $this->assertEqual($sessionManager, $this->layoutManager->setSessionManager($sessionManager));
    }

    //public function testRun() {
    //}

    public function testSetThumbnailCollectionIfNeededWithNoThumbnail() {
        $this->assertNull($this->layoutManager->setThumbnailCollectionIfNeeded());
    }

    public function testSetThumbnailCollectionIfNeededWithThumbnail() {
        $expectedThumbnailCollection = array('photoData' => 'photoData');
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', '1,2', array('thumbnail'));
        $this->layoutManager->setShortcode($shortcode);
        $dataObjectCollection = new MockLib_ShashinAlbumPhotosCollection();
        $dataObjectCollection->setReturnValue('getCollectionForShortcode', $expectedThumbnailCollection);
        $this->layoutManager->setDataObjectCollection($dataObjectCollection);
        $this->assertEqual($expectedThumbnailCollection, $this->layoutManager->setThumbnailCollectionIfNeeded());
    }


    private function setupCollection(array $expectedCollection) {
        $shortcode = new MockPublic_ShashinShortcode();
        $this->layoutManager->setShortcode($shortcode);
        $dataObjectCollection = new MockLib_ShashinAlbumPhotosCollection();
        $dataObjectCollection->setReturnValue('getCollectionForShortcode', $expectedCollection);
        $this->layoutManager->setDataObjectCollection($dataObjectCollection);
        return $expectedCollection;
    }

    public function testSetCollection() {
        $expectedCollection = $this->setupCollection(array('photoData1' => 'photoData1', 'photoData2' => 'photoData2'));
        $this->assertEqual($expectedCollection, $this->layoutManager->setCollection());
    }

    public function testInitializeSessionGroupCounterWithParentTableId() {
        $request = array('shashinParentTableId' => '3');
        $this->layoutManager->setRequest($request);
        $sessionManager = new MockPublic_ShashinSessionManager();
        $sessionManager->setReturnValueAt(0, 'getGroupCounter', null);
        $sessionManager->setReturnValueAt(1, 'getGroupCounter', '3');
        $this->layoutManager->setSessionManager($sessionManager);
        $this->assertEqual('3', $this->layoutManager->initializeSessionGroupCounter());
    }

    public function testInitializeSessionGroupCounterWithNoParentTableId() {
        $request = array('shashinParentTableId' => null);
        $this->layoutManager->setRequest($request);
        $sessionManager = new MockPublic_ShashinSessionManager();
        $sessionManager->setReturnValueAt(0, 'getGroupCounter', null);
        $sessionManager->setReturnValueAt(1, 'getGroupCounter', null);
        $sessionManager->setReturnValueAt(2, 'getGroupCounter', 1);
        $this->layoutManager->setSessionManager($sessionManager);
        $this->assertEqual(1, $this->layoutManager->initializeSessionGroupCounter());
    }

    public function testInitializeSessionGroupCounterWithSessionUnderway() {
        $request = array('shashinParentTableId' => null);
        $this->layoutManager->setRequest($request);
        $sessionManager = new MockPublic_ShashinSessionManager();
        $sessionManager->setReturnValue('getGroupCounter', 6);
        $this->layoutManager->setSessionManager($sessionManager);
        $this->assertEqual(6, $this->layoutManager->initializeSessionGroupCounter());
    }

    public function testSetTotalTablesWithFewerPhotosThanGroupLimit() {
        $this->setupCollection(array('photoData1' => 'photoData1', 'photoData2' => 'photoData2'));
        $settings = new MockLib_ShashinSettings();
        $settings->setReturnValue('__get', '18', array('defaultPhotoLimit'));
        $this->layoutManager->setSettings($settings);
        $this->assertEqual(1, $this->layoutManager->setTotalTables());
    }

    public function testSetTotalTablesWithMorePhotosThanGroupLimit() {
        $this->setupCollection(array('photoData1' => 'photoData1', 'photoData2' => 'photoData2', 'photoData3' => 'photoData3'));
        $this->layoutManager->setCollection();
        $settings = new MockLib_ShashinSettings();
        $settings->setReturnValue('__get', '2', array('defaultPhotoLimit'));
        $this->layoutManager->setSettings($settings);
        $this->assertEqual(2, $this->layoutManager->setTotalTables());
    }

    /*

    public function testSetStartingAndEndingTableGroupCounter() {

    }

    public function testSetOpeningTableTag() {
    }


    public function testAddStyleForOpeningTableTag() {
    }

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
        $settings->setReturnValue('__get',10, 'defaultPhotoLimit');
        $this->layoutManager->setSettings($settings);
        $this->assertNull($this->layoutManager->setTableCaptionTag());
    }

    public function testSetTableCaptionTagWithNextPagination() {
        $collection = new MockLib_ShashinAlbumPhotosCollection();
        $collection->setReturnValue('getMayNeedPagination', true);
        $collection->setReturnValue('getCount', 11);
        $this->layoutManager->setDataObjectCollection($collection);
        $settings = new MockLib_ShashinSettings();
        $settings->setReturnValue('__get',10, 'defaultPhotoLimit');
        $this->layoutManager->setSettings($settings);
        $this->assertEqual('<caption> Next1</caption>' . PHP_EOL, $this->layoutManager->setTableCaptionTag());
    }

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
