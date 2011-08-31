<?php
// these tests also cover methods in the parent ShashinDataObjectCollection class
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaDatabaseFacadeWp.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumCollection.php');
require_once(dirname(__FILE__) . '/../Public/ShashinShortcode.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
Mock::generate('ToppaDatabaseFacadeWp');
Mock::generate('Lib_ShashinAlbum');
Mock::generate('Lib_ShashinSettings');
Mock::generate('Public_ShashinShortcode');

class UnitLib_ShashinAlbumCollection extends UnitTestCase {
    private $albumCollection;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->albumCollection = new Lib_ShashinAlbumCollection();
    }

    public function testSetDbFacade() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $this->assertEqual($dbFacade, $this->albumCollection->setDbFacade($dbFacade));
    }

    public function testSetClonableDataObject() {
        $clonableAlbum = new MockLib_ShashinAlbum();
        $this->assertEqual($clonableAlbum, $this->albumCollection->setClonableDataObject($clonableAlbum));
    }

    public function testSetSettings() {
        $settings = new MockLib_ShashinSettings();
        $this->assertEqual($settings, $this->albumCollection->setSettings($settings));
    }

    public function testSetRequest() {
        $request = array('shashinPage' => 3);
        $this->assertEqual($request, $this->albumCollection->setRequest($request));
    }

    public function testSetUseThumbnailIdWithBoolean() {
        $this->assertTrue($this->albumCollection->setUseThumbnailId(true));
    }

    public function testSetUseThumbnailIdWithInvalidArgument() {
        $this->assertFalse($this->albumCollection->setUseThumbnailId('x'));
    }

    public function testSetNoLimitWithBoolean() {
        $this->assertTrue($this->albumCollection->setNoLimit(true));
    }

    public function testSetNoLimitWithInvalidArgument() {
        $this->assertFalse($this->albumCollection->setNoLimit('x'));
    }

    public function testGetTableName() {
        $tableName = 'wp_shashin_album';
        $clonableAlbum = new MockLib_ShashinAlbum();
        $clonableAlbum->setReturnValue('getTableName', $tableName);
        $this->albumCollection->setClonableDataObject($clonableAlbum);
        $this->assertEqual($tableName, $this->albumCollection->getTableName());
    }

    public function testGetRefData() {
        require('dataFiles/albumRefData.php');
        $clonableAlbum = new MockLib_ShashinAlbum();
        $clonableAlbum->setReturnValue('getRefData', $albumRefData);
        $this->albumCollection->setClonableDataObject($clonableAlbum);
        $this->assertEqual($albumRefData, $this->albumCollection->getRefData());
    }

    //public function testGetCollectionForShortcode() {
    //}

    public function testSetShortcode() {
        $shortcode = new MockPublic_ShashinShortcode();
        $this->assertEqual($shortcode, $this->albumCollection->setShortcode($shortcode));
    }

    //public function testSetProperties() {
    //}

    public function testSetIdStringWithDefault() {
        $shortcode = new MockPublic_ShashinShortcode();
        $id = '1,2';
        $shortcode->setReturnValue('__get', $id, array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual($id, $this->albumCollection->setIdString());
    }

    public function testSetIdStringWithThumbnailId() {
        $shortcode = new MockPublic_ShashinShortcode();
        $id = '1,2';
        $thumbnail = '3,4';
        $shortcode->setReturnValue('__get', $id, array('id'));
        $shortcode->setReturnValue('__get', $thumbnail, array('thumbnail'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setUseThumbnailId(true);
        $this->assertEqual($thumbnail, $this->albumCollection->setIdString());
    }

    public function testSetMayNeedPaginationWithId() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', '1,2', array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $this->assertFalse($this->albumCollection->setMayNeedPagination());
    }

    public function testSetMayNeedPaginationWithNoId() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', null, array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $this->assertTrue($this->albumCollection->setMayNeedPagination());
    }

    public function testSetMayNeedPaginationWithTypeAlbumphotos() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'albumphotos', array('type'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertTrue($this->albumCollection->setMayNeedPagination());
    }

    public function testGetMayNeedPagination() {
        $this->assertFalse($this->albumCollection->getMayNeedPagination());
    }

    public function testSetLimitClauseWithNoLimit() {
        $this->albumCollection->setNoLimit(true);
        $this->assertNull($this->albumCollection->setLimitClause());
    }

    public function testSetLimitClauseWithLimitFromShortcode() {
        $shortcode = new MockPublic_ShashinShortcode();
        $limit = '5';
        $shortcode->setReturnValue('__get', $limit, array('limit'));
        $this->albumCollection->setShortcode($shortcode);
        $expectedResult = " limit $limit";
        $this->assertEqual($expectedResult, $this->albumCollection->setLimitClause());
    }

    public function testSetLimitClauseWithPhotosPerPageSetting() {
        $settings = new MockLib_ShashinSettings();
        $photosPerPage = '5';
        $settings->setReturnValue('__get', $photosPerPage, array('photosPerPage'));
        $this->albumCollection->setSettings($settings);
        $request = array('shashinPage' => '3');
        $this->albumCollection->setRequest($request);
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'albumphotos', array('type'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setMayNeedPagination();
        $expectedResult = " limit $photosPerPage offset 10";
        $this->assertEqual($expectedResult, $this->albumCollection->setLimitClause());
    }

    public function testSetLimitClauseWithIdString() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', '1,2', array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $this->assertNull($this->albumCollection->setLimitClause());
    }

    public function testSetSortWithDefaultSort() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', null, array('reverse'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('asc', $this->albumCollection->setSort());

    }

    public function testSetSortWithReverseSort() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'y', array('reverse'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('desc', $this->albumCollection->setSort());
    }

    public function testSetOrderByWithNoShortcode() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', null, array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('pubDate', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByWithIdString() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', null, array('order'));
        $shortcode->setReturnValue('__get', '1,2', array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $this->assertEqual('user', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderById() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'id', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('id', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByDate() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'date', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('pubDate', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByFileName() {
        try {
            $shortcode = new MockPublic_ShashinShortcode();
            $shortcode->setReturnValue('__get', 'filename', array('order'));
            $this->albumCollection->setShortcode($shortcode);
            $this->albumCollection->setOrderBy();
            $this->fail("Exception was expected");
         }

         catch (Exception $e) {
             $this->pass("received expected exception");
         }
    }

    public function testSetOrderByTitle() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'title', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('title', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByLocation() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'location', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('location', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByCount() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'count', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('photoCount', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderBySync() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'sync', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('lastSync', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByRandom() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'random', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('rand()', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderBySource() {
        try {
            $shortcode = new MockPublic_ShashinShortcode();
            $shortcode->setReturnValue('__get', 'source', array('order'));
            $this->albumCollection->setShortcode($shortcode);
            $this->albumCollection->setOrderBy();
            $this->fail("Exception was expected");
         }

         catch (Exception $e) {
             $this->pass("received expected exception");
         }
    }

    public function testSetOrderByUser() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'user', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->assertEqual('user', $this->albumCollection->setOrderBy());
    }

    public function testSetOrderByClauseWithUser() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'user', array('order'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setOrderBy();
        $this->assertNull($this->albumCollection->setOrderByClause());
    }

    public function testSetOrderByClauseWithDateReverse() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', 'date', array('order'));
        $shortcode->setReturnValue('__get', 'y', array('reverse'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setOrderBy();
        $this->albumCollection->setSort();
        $expectedResult = 'order by pubDate desc';
        $this->assertEqual($expectedResult, $this->albumCollection->setOrderByClause());
    }

    public function testSetWhereClause() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', '1,2', array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $expectedResult = 'where id in (1,2)';
        $this->assertEqual($expectedResult, $this->albumCollection->setWhereClause());
    }

    public function testSetWhereClauseForAlbumPhotos() {
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', '1,2', array('id'));
        $shortcode->setReturnValue('__get', 'albumphotos', array('type'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $expectedResult = 'where albumId in (1,2)';
        $this->assertEqual($expectedResult, $this->albumCollection->setWhereClause());
    }

    //public function testSetSqlConditions() {
    //}

    private function getDataSetup() {
        require('dataFiles/sampleAlbumDataPicasa.php');
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('sqlSelectMultipleRows', $albumData);
        $this->albumCollection->setDbFacade($dbFacade);
        $clonableAlbum = new MockLib_ShashinAlbum();
        $clonableAlbum->setReturnValue('getTableName', 'wp_shashin_album');
        $this->albumCollection->setClonableDataObject($clonableAlbum);
        return $albumData;
    }

    public function testGetData() {
        $albumData = $this->getDataSetup();
        $rows = $this->albumCollection->getData();
        $this->assertEqual($albumData, $rows);
    }

    public function testSetCount() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('sqlSelectRow', array('count' => 41));
        $this->albumCollection->setDbFacade($dbFacade);
        $clonableAlbum = new MockLib_ShashinAlbum();
        $clonableAlbum->setReturnValue('getTableName', 'wp_shashin_album');
        $this->albumCollection->setClonableDataObject($clonableAlbum);
        $this->assertEqual(41, $this->albumCollection->setCount());
    }

    public function testGetCount() {
        $this->assertNull($this->albumCollection->getCount());
    }

    public function testGetCollection() {
        $this->getDataSetup();
        $result = $this->albumCollection->getCollection();
        $this->assertTrue(is_a($result[0], 'Lib_ShashinDataObject'));
    }

    public function testGetCollectionInUserOrder() {
        $this->getDataSetup();
        $shortcode = new MockPublic_ShashinShortcode();
        $shortcode->setReturnValue('__get', '2,1', array('id'));
        $this->albumCollection->setShortcode($shortcode);
        $this->albumCollection->setIdString();
        $result = $this->albumCollection->getCollectionInUserOrder();
        $this->assertEqual(count($result), 2);
        $this->assertTrue(is_a($result[0], 'Lib_ShashinDataObject'));
    }
}