<?php

require_once(dirname(__FILE__) . '/../Public/ShashinShortcode.php');

class UnitPublic_Shortcode extends UnitTestCase {
    private $invalidTestCases = array(
        'type' => 'ahoy',
        'id' => '1,2,b,3',
        'size' => 'n',
        'format' => 'nonsense',
        'caption' => 'x',
        'limit' => 'z',
        'order' => 'y',
        'reverse' => '42',
        'position' => 'a',
        'clear' => 'b',
        'thumbnail' => '1,2,b,3'
    );

    private $validTestCase = array(
        'type' => 'photo',
        'id' => '1,2,3',
        'order' => 'Date',
        'reverse' => 'Y',
        'limit' => '3 ');

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {

    }

    public function testCleanShortcode() {
        $shortcode = new Public_ShashinShortcode($this->validTestCase);
        $cleanShortcode = $shortcode->cleanShortcode();
        $this->assertEqual($cleanShortcode['type'], 'photo');
        $this->assertEqual($cleanShortcode['order'], 'date');
        $this->assertEqual($cleanShortcode['reverse'], 'y');
        $this->assertEqual($cleanShortcode['limit'], '3');
    }
}