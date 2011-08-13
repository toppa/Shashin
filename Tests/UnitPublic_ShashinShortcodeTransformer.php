<?php

require_once(dirname(__FILE__) . '/../Public/ShashinShortcode.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinContainer.php');
Mock::generate('Lib_ShashinContainer');

class UnitPublic_ShortcodeTransformer extends UnitTestCase {
    private $container;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->container = new MockLib_ShashinContainer();
    }

    public function testCleanShortcode() {
        $testCase = array('type' => 'photo ', 'id' => '1,2,3', 'order' => 'Date', 'reverse' => 'Y', 'format' => ' table', 'limit' => '3 ');
        $transformer = new Public_ShashinShortcode($testCase, $this->container);
        $cleanShortcode = $transformer->cleanShortcode();
        $this->assertEqual($cleanShortcode['type'], 'photo');
        $this->assertEqual($cleanShortcode['order'], 'date');
        $this->assertEqual($cleanShortcode['reverse'], 'y');
        $this->assertEqual($cleanShortcode['format'], 'table');
        $this->assertEqual($cleanShortcode['limit'], '3');
    }
}