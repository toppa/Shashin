<?php

require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');

class IntegrationAdmin_ShashinContainer extends UnitTestCase {
    private $autoLoader;

    public function __construct() {
    }

    public function setUp() {
        $this->autoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
    }

    public function testGetInstaller() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $installer = $adminContainer->getInstaller();
        $this->assertTrue($installer instanceof Admin_ShashinInstaller);
    }

    public function testGetUninstaller() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $uninstaller = $adminContainer->getUninstaller();
        $this->assertTrue($uninstaller instanceof Admin_ShashinUninstaller);
    }

    public function testGetMenuDisplayerPhotos() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $menuDisplayerPhotos = $adminContainer->getMenuDisplayerPhotos(1);
        $this->assertTrue($menuDisplayerPhotos instanceof Admin_ShashinMenuDisplayerPhotos);
    }

    public function testGetMenuDisplayerPhotosWithNoValidAlbumKey() {
        try {
            $adminContainer = new Admin_ShashinContainer($this->autoLoader);
            $menuDisplayerPhotos = $adminContainer->getMenuDisplayerPhotos(null);
            $this->fail("Exception expected - no valid album key given");
        }

        catch (Exception $e) {
            $this->pass("Received expected exception");
        }
    }

    public function testGetMenuDisplayerAlbums() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $menuDisplayerAlbums = $adminContainer->getMenuDisplayerAlbums();
        $this->assertTrue($menuDisplayerAlbums instanceof Admin_ShashinMenuDisplayerAlbums);
    }

    public function testGetMenuActionHandlerPhotos() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $menuActionHandlerPhotos = $adminContainer->getMenuActionHandlerPhotos(1);
        $this->assertTrue($menuActionHandlerPhotos instanceof Admin_ShashinMenuActionHandlerPhotos);
    }

    public function testGetMenuActionHandlerPhotosWithNoValidAlbumKey() {
        try {
            $adminContainer = new Admin_ShashinContainer($this->autoLoader);
            $menuActionHandlerPhotos = $adminContainer->getMenuActionHandlerPhotos(null);
            $this->fail("Exception expected - no valid album key given");
        }

        catch (Exception $e) {
            $this->pass("Received expected exception");
        }
    }

    public function testGetMenuActionHandlerAlbums() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $menuActionHandlerAlbums = $adminContainer->getMenuActionHandlerAlbums();
        $this->assertTrue($menuActionHandlerAlbums instanceof Admin_ShashinMenuActionHandlerAlbums);
    }
}