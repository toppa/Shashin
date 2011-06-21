<?php

require_once(dirname(__FILE__) . '/../Public/ShashinShortcodeTransformer.php');

class UnitPublic_ShortcodeTransformer extends UnitTestCase {
    private $validTestCases = array(
        'photosKeysXsmallThumbs' => array('type' => 'photos', 'keys' => '1,2,3', 'size' => 'x-small', 'thumbnails' => '4,5,6'),
        'keysMediumCenter' => array('keys' => '1,2,3', 'size' => 'medium', 'position' => 'center'),
        'albumsKeysNaturalClear' => array('type' => 'albums', 'keys' => '1,2,3', 'order' => 'natural', 'clear' => 'both'),
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

    public function testAssignDefaultValuesIfEmpty() {
        $transformer = new Public_ShashinShortcodeTransformer($this->validTestCases['keysMediumCenter']);
        $transformer->assignDefaultValuesIfEmpty();
        $thisShortcode = $transformer->getShortcode();
        $this->assertEqual('photos', $thisShortcode['type']);

        $transformer = new Public_ShashinShortcodeTransformer($this->validTestCases['photosKeysXsmallThumbs']);
        $transformer->assignDefaultValuesIfEmpty();
        $thisShortcode = $transformer->getShortcode();
        $this->assertEqual('natural', $thisShortcode['order']);
    }
}