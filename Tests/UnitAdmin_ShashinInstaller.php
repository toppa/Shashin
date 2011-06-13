<?php

// can't use the autoloader when mocking :-(
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaDatabaseFacadeWp.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinDataObject.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinPhoto.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinSettings.php');
require_once(dirname(__FILE__) . '/../Admin/ShashinInstaller.php');
Mock::generate('ToppaDatabaseFacadeWp');
Mock::generate('Lib_ShashinAlbum');
Mock::generate('Lib_ShashinPhoto');
Mock::generate('Lib_ShashinSettings');

class UnitAdmin_ShashinInstaller extends UnitTestCase {
    private $installer;
    private $dbFacade;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->dbFacade = new MockToppaDatabaseFacadeWp();
        $album = new MockLib_ShashinAlbum($this->dbFacade);
        $album->setReturnValue('getTableName', 'wp_shashin_album_3alpha');
        $photo = new MockLib_ShashinPhoto($this->dbFacade);
        $photo->setReturnValue('getTableName', 'wp_shashin_photo_3alpha');
        $settings = new MockLib_ShashinSettings($this->dbFacade);
        $refData =
            array('test_column' => array(
                'col_params' => array(
                    'type' => 'varchar',
                    'length' => '20',
                    'not_null' => true)
                ),
            );
        $album->setReturnValue('getRefData', $refData);
        $photo->setReturnValue('getRefData', $refData);
        $this->dbFacade->setReturnValue('verifyTableExists', true);
        $this->installer = new Admin_ShashinInstaller($this->dbFacade, $album, $photo, $settings);
    }

    public function testCreateAndVerifyTables() {
        $this->assertTrue($this->installer->createAndVerifyTables());
    }
}