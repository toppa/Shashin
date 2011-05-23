<?php

require_once(dirname(__FILE__) . '/../../toppa-libs/ToppaWpDatabaseFacade.php');
require_once(dirname(__FILE__) . '/../ShashinAlbumRef.php');
Mock::generate('ToppaWpDatabaseFacade');

class UnitShashinAlbumRef extends UnitTestCase {
    private $albumRef;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $dbFacade = &new MockToppaWpDatabaseFacade();
        $dbFacade->setReturnValue('getTableNamePrefix', 'wp_');
        $this->albumRef = new ShashinAlbumRef($dbFacade);
    }

    public function testGetRefData() {
        $refData = $this->albumRef->getRefData();
        $this->assertTrue(is_array($refData));
        $this->assertFalse(empty($refData));
    }

    public function testGetTableName() {
        $this->assertEqual($this->albumRef->getTableName(), 'wp_shashin_album_3alpha');
    }
}