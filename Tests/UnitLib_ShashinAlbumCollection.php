<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaDatabaseFacadeWp.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumCollection.php');
Mock::generate('ToppaDatabaseFacadeWp');
Mock::generate('Lib_ShashinAlbum');


class UnitLib_ShashinShortcodeValidator extends UnitTestCase {
    private $albumCollection;

    private $validTestCases = array(
        'photoIdXsmallThumbs' => array('type' => 'photo', 'id' => '1,2,3', 'size' => 'x-small', 'thumbnails' => '4,5,6'),
        'photoIdMediumCenter' => array('type' => 'photo', 'id' => '1,2,3', 'size' => 'medium', 'position' => 'center'),
        'albumIdUserClear' => array('type' => 'album', 'id' => '1,3,2', 'order' => 'user', 'clear' => 'both'),
        'photo160RandomListCaption' => array('type' => 'photo', 'size' => 160, 'order' => 'random', 'format' => 'list', 'caption' => 'y'),
        'photoIdDateReverseTableLimit' => array('type' => 'photo', 'id' => '1,2,3', 'order' => 'date', 'reverse' => 'y', 'format' => 'table', 'limit' => 3),
    );
    private $invalidTestCases = array(
        'invalid' => array(
            'type' => 'ahoy',
            'id' => '1,2,b,3',
            'size' => 'n',
            'format' => 'nonsense',
            'caption' => 'x',
            'limit' => 'z',
            'order' => 'y',
            'reverse' => 42,
            'position' => 'a',
            'clear' => 'b',
            'thumbnails' => '1,2,b,3'
        ),
    );

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('getTableNamePrefix', 'wp_');
        $clonableAlbum = new MockLib_ShashinAlbum();
        $this->albumCollection = new Lib_ShashinAlbumCollection($dbFacade, $clonableAlbum);
    }

    public function testSetType() {
        try {
            $this->albumCollection->setType($this->invalidTestCases['invalid']['type']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }

        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setType($case['type']));
        }

    }

    /*
    public function testValidateId() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setId($case['id']));
        }

        try {
            $this->albumCollection->setId($this->invalidTestCases['invalid']['id']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateSize() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setSize($case['size']));
        }

        try {
            $this->albumCollection->setSize($this->invalidTestCases['invalid']['size']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateFormat() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setFormat($case['format']));
        }

        try {
            $this->albumCollection->setSize($this->invalidTestCases['invalid']['format']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateCaption() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setCaption($case['caption']));
        }

        try {
            $this->albumCollection->setCaption($this->invalidTestCases['invalid']['caption']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateLimit() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setLimit($case['limit']));
        }

        try {
            $this->albumCollection->setLimit($this->invalidTestCases['invalid']['limit']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateOrder() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setOrder($case['order']));
        }

        try {
            $this->albumCollection->setOrder($this->invalidTestCases['invalid']['order']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateReverse() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setReverse($case['reverse']));
        }

        try {
            $this->albumCollection->setReverse($this->invalidTestCases['invalid']['reverse']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidatePosition() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setPosition($case['position']));
        }

        try {
            $this->albumCollection->setPosition($this->invalidTestCases['invalid']['position']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateClear() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setClear($case['clear']));
        }

        try {
            $this->albumCollection->setClear($this->invalidTestCases['invalid']['clear']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateThumbnails() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setThumbnails($case['thumbnails']));
        }

        try {
            $this->albumCollection->setThumbnails($this->invalidTestCases['invalid']['thumbnails']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }
    */
}