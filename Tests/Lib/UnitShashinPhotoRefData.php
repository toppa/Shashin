<?php

class UnitShashinAlbumRefData extends UnitTestCase {
    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
    }

    public function testGetRefData() {
        $albumRefData = new Lib_ShashinAlbumRefData();
        $refData = $albumRefData->getRefData();
        // spot check a field
        $this->assertTrue($refData['id']['db']['primary_key']);
    }
}