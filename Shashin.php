<?php

require_once('ShashinInstaller.php');
require_once('ShashinObjectsSetup.php');
require_once('Album/ShashinAlbumRef.php');
require_once('Photo/ShashinPhotoRef.php');
require_once('Shortcode/ShashinShortcodeValidator.php');
require_once('Shortcode/ShashinShortcodeTransformer.php');
require_once('AdminMenus/ShashinMenuDisplayerPhotos.php');
require_once('AdminMenus/ShashinMenuActionHandlerPhotos.php');
require_once('AdminMenus/ShashinMenuDisplayerAlbums.php');
require_once('AdminMenus/ShashinMenuActionHandlerAlbums.php');


class Shashin3Alpha {
    private $hooksFacade;
    private $functionsFacade;
    private $dbFacade;
    private $settings;
    private $version = '3.0_alpha';

    public function __construct(&$hooksFacade, &$functionsFacade, &$dbFacade, &$settings) {
        $this->hooksFacade = $hooksFacade;
        $this->functionsFacade = $functionsFacade;
        $this->dbFacade = $dbFacade;
        $this->settings = $settings;
    }

    public function install() {
        $albumRef = new ShashinAlbumRef($this->dbFacade);
        $photoRef = new ShashinPhotoRef($this->dbFacade);
        $installer = new ShashinInstaller($this->dbFacade, $albumRef, $photoRef, $this->settings);
        return $installer->run();
    }

    public function run() {
        $this->hooksFacade->addShortcode('shashin', array($this, 'handleShortcode'));
        $this->hooksFacade->addAction('admin_menu', array($this, 'initAdminMenus'));
    }

    public function handleShortcode($shortcode) {
        $photoSet = $objectsSetup->setupPhotoSet();
        $transformer = new ShashinShortcodeTransformer($shortcode);
        $cleanShortcode = $transformer->cleanShortcode();
        $validator = new ShashinShortcodeValidator($cleanShortcode);
        $validatorResult = $validator->run();

        if ($validatorResult !== true) {
            return $validatorResult;
        }

        return $transformer->run();
    }

    public function initAdminMenus() {
        $toolsPage = $this->hooksFacade->createManagementPage(
            'Shashin3Alpha',
            'Shashin3Alpha',
            'edit_posts',
            'Shashin3AlphaToolsMenu',
            array($this, 'generateToolsMenu')
        );

        // from http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/
        $this->hooksFacade->addAction("admin_print_styles-$toolsPage", array($this, 'generateAdminPagesHeadTags'));
    }

    public function generateToolsMenu() {
        $objectsSetup = new ShashinObjectsSetup($this->dbFacade, $this->functionsFacade);

        if ($_REQUEST['shashinMenu'] == 'photos') {
            if ($_REQUEST['switchingFromAlbumsMenu']) {
                $this->functionsFacade->checkAdminNonceFields("shashinNoncePhotosMenu_" . $_REQUEST['albumKey']);
            }
            $album = $objectsSetup->setupAlbum($_REQUEST['albumKey']);
            $photoRef = $objectsSetup->setupPhotoRef();
            $photoDisplayer = $objectsSetup->setupPhotoDisplayer($album);
            $menuDisplayer = new ShashinMenuDisplayerPhotos($this->functionsFacade, $_REQUEST, $photoRef, $album, $photoDisplayer);
            $menuActionHandler = new ShashinMenuActionHandlerPhotos($this->functionsFacade, $menuDisplayer, $objectsSetup, $_REQUEST);
        }

        else {
            $albumRef = $objectsSetup->setupAlbumRef();
            $albumSet = $objectsSetup->setupAlbumSet();
            $menuDisplayer = new ShashinMenuDisplayerAlbums($this->functionsFacade, $_REQUEST, $albumRef, $albumSet);
            $menuActionHandler = new ShashinMenuActionHandlerAlbums($this->functionsFacade, $menuDisplayer, $objectsSetup, $_REQUEST);
        }

        $menuActionHandler->run();
    }

    public function generateAdminPagesHeadTags() {
        $adminCssUrl = $this->functionsFacade->getUrlforCustomizableFile('admin.css', __FILE__, 'AdminMenus/Display/');
        $adminJsUrl = $this->functionsFacade->getUrlforCustomizableFile('admin.js', __FILE__, 'AdminMenus/Display/');
        $this->hooksFacade->enqueueStylesheet('shashin_admin_css', $adminCssUrl, $this->version);
        $this->hooksFacade->enqueueScript('shashin_admin_js', $adminJsUrl, array('jquery'), $this->version);
        $this->hooksFacade->localizeScript(
            'shashin_admin_js',
            'shashin_display',
            array('url' => $this->functionsFacade->getPluginsUrl('AdminMenus/Display/', __FILE__))
        );
    }
}