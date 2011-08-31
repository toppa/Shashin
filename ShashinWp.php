<?php

class ShashinWp {
    private $version = '3.0';
    private $autoLoader;

    public function __construct(ToppaAutoLoader &$autoLoader) {
        $this->autoLoader = $autoLoader;
    }

    public function run() {
        add_action('admin_menu', array($this, 'initToolsMenu'));
        add_action('admin_menu', array($this, 'initSettingsMenu'));
        add_action('template_redirect', array($this, 'writePublicJsAndCss'));
        add_shortcode('shashin', array($this, 'handleShortcode'));
        add_action('wp_ajax_nopriv_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('wp_ajax_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
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
            array($this, 'writeToolsMenu')
        );

        // from http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/
        add_action("admin_print_styles-$toolsPage", array($this, 'writeToolsMenuJsAndCss'));
    }

    public function writeToolsMenu() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);

        if ($_REQUEST['shashinMenu'] == 'photos') {
            $menuActionHandler = $adminContainer->getMenuActionHandlerPhotos($_REQUEST['id']);
        }

        else {
            $menuActionHandler = $adminContainer->getMenuActionHandlerAlbums();
        }

        echo $menuActionHandler->run();
    }

    public function initSettingsMenu() {
        add_options_page(
            'Shashin3Alpha',
            'Shashin3Alpha',
            'manage_options',
            'shashin3alpha',
            array($this, 'writeSettingsMenu')
        );
    }

    public function writeSettingsMenu() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $settingsDisplayer = $adminContainer->getSettingsDisplayer();
        echo $settingsDisplayer->run();
    }

    public function writeToolsMenuJsAndCss() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $docHeadUrlsFetcher = $adminContainer->getDocHeadUrlsFetcher();
        $cssUrl = $docHeadUrlsFetcher->getCssUrl();
        wp_enqueue_style('shashinAdminStyle', $cssUrl, false, $this->version);
        $jsUrl = $docHeadUrlsFetcher->getJsUrl();
        wp_enqueue_script('shashinAdminScript', $jsUrl, array('jquery'), $this->version);
        $menuDisplayUrl = $docHeadUrlsFetcher->getMenuDisplayUrl();
        wp_localize_script('shashinAdminScript', 'shashinDisplay', array('url' => $menuDisplayUrl));
    }

    public function writePublicJsAndCss() {
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $docHeadUrlsFetcher = $publicContainer->getDocHeadUrlsFetcher();
        $shashinCssUrl = $docHeadUrlsFetcher->getShashinCssUrl();
        wp_enqueue_style('shashinStyle', $shashinCssUrl, false, $this->version);
        $baseUrl = plugins_url('Public/Display/', __FILE__);
        wp_enqueue_script(
            'shashinAlbumPhotosDisplayer',
            $baseUrl . 'albumPhotosDisplayer.js',
            array('jquery'),
            $this->version
        );
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $albumPhotosDisplayerParams = array('ajaxurl' => admin_url('admin-ajax.php', $protocol));
        wp_localize_script('shashinAlbumPhotosDisplayer', 'shashinAlbumPhotosDisplayer', $albumPhotosDisplayerParams);
        $settings = $publicContainer->getSettings();

        if ($settings->imageDisplay == 'highslide') {
            $highslideCssUrl = $docHeadUrlsFetcher->getHighslideCssUrl();
            wp_enqueue_style('highslideStyle', $highslideCssUrl, false, '4.1.12');
            wp_enqueue_script('highslide', $baseUrl . 'highslide/highslide.js', false, '4.1.12');
            wp_enqueue_script('swfobject', $baseUrl . 'highslide/swfobject.js', false, '2.2');
            wp_enqueue_script('highslideSettings', $baseUrl . 'highslideSettings.js', false, $this->version);
            wp_localize_script('highslideSettings', 'highslideSettings', array(
                'graphicsDir' => $baseUrl . 'highslide/graphics/',
                'outlineType' => $settings->highslideOutlineType,
                'dimmingOpacity' => $settings->highslideDimmingOpacity,
                'interval' => $settings->highslideInterval,
                'repeat' => $settings->highslideRepeat,
                'position' => $settings->highslideVPosition . ' ' . $settings->highslideHPosition,
                'hideController' => $settings->highslideHideController
            ));
        }

    }

    public function handleShortcode($rawShortcode) {
        try {
            // if the shortcode has no attributes specified, WP passes
            // an empty string instead of an array
            if (!is_array($rawShortcode)) {
                $rawShortcode = array();
            }

            $publicContainer = new Public_ShashinContainer($this->autoLoader);
            $shortcode = $publicContainer->getShortcode($rawShortcode);

            switch ($shortcode->type) {
                case 'photo':
                case null:
                case '':
                    $dataObjectCollection = $publicContainer->getClonablePhotoCollection();
                    break;
                case 'albumphotos':
                    $dataObjectCollection = $publicContainer->getClonableAlbumPhotosCollection();
                    break;
                case 'album':
                    $dataObjectCollection = $publicContainer->getClonableAlbumCollection();
                    break;
                default:
                    return __('Invalid shashin shortcode type: ', 'shashin') . htmlentities($shortcode->type());
            }

            $layoutManager = $publicContainer->getLayoutManager($shortcode, $dataObjectCollection);
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return $layoutManager->run();
    }

    public function ajaxDisplayAlbumPhotos() {
        echo '<table id="shashinGroup1"><tr><td>HELLO WORLD!</td></tr></table>';
        die();
    }

}

