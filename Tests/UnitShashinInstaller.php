<?php

require_once(dirname(__FILE__) . '/../../toppa-libs/ToppaWpDatabaseFacade.php');
require_once(dirname(__FILE__) . '/../ShashinAlbumRef.php');
require_once(dirname(__FILE__) . '/../ShashinPhotoRef.php');
require_once(dirname(__FILE__) . '/../ShashinSettings.php');
require_once(dirname(__FILE__) . '/../ShashinInstaller.php');
Mock::generate('ToppaWpDatabaseFacade');
Mock::generate('ShashinAlbumRef');
Mock::generate('ShashinPhotoRef');
Mock::generate('ShashinSettings');

class UnitShashinInstaller extends UnitTestCase {
    private $installer;
    private $dbFacade;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->dbFacade = new MockToppaWpDatabaseFacade();
        $albumRef = new MockShashinAlbumRef($this->dbFacade);
        $albumRef->setReturnValue('getTableName', 'wp_shashin_album_3alpha');
        $photoRef = new MockShashinPhotoRef($this->dbFacade);
        $photoRef->setReturnValue('getTableName', 'wp_shashin_photo_3alpha');
        $settings = new MockShashinSettings($this->dbFacade);
        $refData =
            array('test_column' => array(
                'col_params' => array(
                    'type' => 'varchar',
                    'length' => '20',
                    'not_null' => true)
                ),
            );
        $albumRef->setReturnValue('getRefData', $refData);
        $this->dbFacade->setReturnValue('verifyTableExists', true);
        $this->installer = new ShashinInstaller($this->dbFacade, $albumRef, $photoRef, $settings);
    }

    public function testCreateAndVerifyTables() {
        $this->assertTrue($this->installer->createAndVerifyTables());
    }
}