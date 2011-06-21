<?php

class ShashinWp {
    private $version = '3.0';
    private $autoLoader;

    public function __construct(ToppaAutoLoader &$autoLoader) {
        $this->autoLoader = $autoLoader;
    }

    public function run() {
        add_action('admin_menu', array($this, 'initToolsMenu'));
        add_shortcode('shashin', array($this, 'handleShortcode'));
    }

    public function getVersion() {
        return $this->version;
    }

    public function install() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $installer = $adminContainer->getInstaller();
        $activationStatus = $installer->run();

        if ($activationStatus !== true) {
            // trigger_error is how you indicate an activation problem in WordPress
            trigger_error(__('Activation failed: ', 'shashin') . $activationStatus, E_USER_ERROR);
        }
    }

    public function initToolsMenu() {
        $toolsPage = add_management_page(
            'Shashin3Alpha',
            'Shashin3Alpha',
            'edit_posts',
            'Shashin3AlphaToolsMenu',
            array($this, 'buildToolsMenu')
        );

        // from http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/
        add_action("admin_print_styles-$toolsPage", array($this, 'buildAdminHeadTags'));
    }


    public function buildToolsMenu() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);

        if ($_REQUEST['shashinMenu'] == 'photos') {
            $menuActionHandler = $adminContainer->getMenuActionHandlerPhotos($_REQUEST['albumKey']);
        }

        else {
            $menuActionHandler = $adminContainer->getMenuActionHandlerAlbums();
        }

        $menuActionHandler->run();
    }

    public function buildAdminHeadTags() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $docHeadUrlsFetcher = $adminContainer->getDocHeadUrlsFetcher();
        $cssUrl = $docHeadUrlsFetcher->getCssUrl();
        wp_enqueue_style('shashin_admin_css', $cssUrl, false, $this->version);
        $jsUrl = $docHeadUrlsFetcher->getJsUrl();
        wp_enqueue_script('shashin_admin_js', $jsUrl, array('jquery'), $this->version);
        $menuDisplayUrl = $docHeadUrlsFetcher->getMenuDisplayUrl();
        wp_localize_script('shashin_admin_js', 'shashin_display', array('url' => $menuDisplayUrl));
    }

    public function handleShortcode($shortcode) {
        $autoLoader = new Public_ShashinContainer($this->autoLoader);
        $transformer = new ShashinShortcodeTransformer($shortcode);
        $cleanShortcode = $transformer->cleanShortcode();
        $validator = new ShashinShortcodeValidator($cleanShortcode);
        $validatorResult = $validator->run();

        if ($validatorResult !== true) {
            return $validatorResult;
        }

        if ($cleanShortcode['type'] == 'albums') {
            $albumCollection = $autoLoader->getClonableAlbumCollection();
            $transformer->setDataObjectSet($albumCollection);
        }

        else {
            $photoCollection = $autoLoader->getClonablePhotoCollection();
            $transformer->setDataObjectSet($photoCollection);
        }

        return $transformer->run();
    }
}

