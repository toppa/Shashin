<?php

require_once(dirname(__FILE__) . '/../Public/ShashinShortcodeValidator.php');

class UnitPublic_ShashinShortcodeValidator extends UnitTestCase {
    private $validTestCases = array(
        'photosKeysXsmallThumbs' => array('type' => 'photos', 'keys' => '1,2,3', 'size' => 'x-small', 'thumbnails' => '4,5,6'),
        'keysMediumCenter' => array('keys' => '1,2,3', 'size' => 'medium', 'position' => 'center'),
        'albumsKeysServerClear' => array('type' => 'albums', 'keys' => '1,2,3', 'order' => 'server', 'clear' => 'both'),
        'random160ListCaption' => array('type' => 'random', 'size' => 160, 'format' => 'list', 'caption' => 'y'),
        'newKeysPubdateTable' => array('type' => 'new', 'keys' => '1,2,3', 'order' => 'pub_date', 'format' => 'table', 'count' => 3),
    );
    private $invalidTestCases = array(
        'invalid' => array(
            'type' => 'ahoy',
            'keys' => '1,2,b,3',
            'size' => 'n',
            'format' => 'nonsense',
            'caption' => 'x',
            'count' => 'z',
            'order' => 'y',
            'position' => 'a',
            'clear' => 'b',
            'thumbnails' => '1,2,b,3'
        ),
    );

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
    }

    public function testValidateType() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateType());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateType();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateKeys() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateKeys());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateKeys();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateSize() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateSize());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateSize();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateFormat() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateFormat());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateSize();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateCaption() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateCaption());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateCaption();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateCount() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateCount());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateCount();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateOrder() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateOrder());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateOrder();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidatePosition() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validatePosition());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validatePosition();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateClear() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateClear());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateClear();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateThumbnails() {
        foreach ($this->validTestCases as $case) {
            $validator = new Public_ShashinShortcodeValidator($case);
            $this->assertTrue($validator->validateThumbnails());
        }

        try {
            $validator = new Public_ShashinShortcodeValidator($this->invalidTestCases['invalid']);
            $validator->validateThumbnails();
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }
}