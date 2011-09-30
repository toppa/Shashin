<?php

class ShashinWp {
    private $version = '3.0';
    private $autoLoader;

    public function __construct(ToppaAutoLoader $autoLoader) {
        $this->autoLoader = $autoLoader;
    }

    public function getVersion() {
        return $this->version;
    }

    public function install() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $installer = $adminContainer->getInstaller();
        $activationStatus = $installer->run();

        if ($activationStatus !== true) {
            wp_die(__('Activation of Shashin failed. Error Message: ', 'shashin') . $activationStatus);
        }
    }

    public function run() {
        add_action('admin_menu', array($this, 'initToolsMenu'));
        add_action('admin_menu', array($this, 'initSettingsMenu'));
        add_action('template_redirect', array($this, 'displayPublicJsAndCss'));
        add_shortcode('shashin', array($this, 'handleShortcode'));
        add_action('wp_ajax_nopriv_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('wp_ajax_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('media_buttons', array($this, 'addShashinMediaButton'), 20);
        add_action('media_upload_shashin_photos', array($this, 'initPhotoMediaMenu'));
        add_action('media_upload_shashin_albums', array($this, 'initAlbumMediaMenu'));
        add_action('wp_ajax_shashinGetPhotosForMediaMenu', array($this, 'ajaxGetPhotosForMediaMenu'));

    }

    public function initToolsMenu() {
        $toolsPage = add_management_page(
            'Shashin3Alpha',
            'Shashin3Alpha',
            'edit_posts',
            'Shashin3AlphaToolsMenu',
            array($this, 'displayToolsMenu')
        );

        // from http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/
        add_action("admin_print_styles-$toolsPage", array($this, 'displayToolsMenuJsAndCss'));
    }

    public function displayToolsMenu() {
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
            array($this, 'displaySettingsMenu')
        );
    }

    public function displaySettingsMenu() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $settingsMenuManager = $adminContainer->getSettingsMenuManager();
        echo $settingsMenuManager->run();
    }

    public function displayToolsMenuJsAndCss() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $docHeadUrlsFetcher = $adminContainer->getDocHeadUrlsFetcher();
        $cssUrl = $docHeadUrlsFetcher->getCssUrl();
        wp_enqueue_style('shashinAdminStyle', $cssUrl, false, $this->version);
        $jsUrl = $docHeadUrlsFetcher->getJsUrl();
        wp_enqueue_script('shashinAdminScript', $jsUrl, array('jquery'), $this->version);
        $menuDisplayUrl = $docHeadUrlsFetcher->getMenuDisplayUrl();
        wp_localize_script('shashinAdminScript', 'shashinDisplay', array('url' => $menuDisplayUrl));
    }

    public function displayPublicJsAndCss() {
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $docHeadUrlsFetcher = $publicContainer->getDocHeadUrlsFetcher();
        $shashinCssUrl = $docHeadUrlsFetcher->getShashinCssUrl();
        wp_enqueue_style('shashinStyle', $shashinCssUrl, false, $this->version);
        $baseUrl = plugins_url('Public/Display/', __FILE__);
        wp_enqueue_script(
            'shashinPhotoGroupsDisplayer',
            $baseUrl . 'photoGroupsDisplayer.js',
            array('jquery'),
            $this->version
        );
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $photoGroupsDisplayerParams = array('ajaxurl' => admin_url('admin-ajax.php', $protocol));
        wp_localize_script('shashinPhotoGroupsDisplayer', 'shashinPhotoGroupsDisplayer', $photoGroupsDisplayerParams);
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

            $layoutManager = $publicContainer->getLayoutManager($shortcode, $dataObjectCollection, $_REQUEST);
            return $layoutManager->run();
        }

        catch (Exception $e) {
            return '<strong>' . __('Shashin Error: ', 'shashin') . $e->getMessage() . '<strong>';
        }
    }

    public function ajaxDisplayAlbumPhotos() {
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $settings = $publicContainer->getSettings();
        $shortcode = array(
            'type' => 'albumphotos',
            'id' => $_REQUEST['shashinAlbumId'],
            'size' => $settings->albumPhotosSize,
            'crop' => $settings->albumPhotosCrop,
            'columns' => $settings->albumPhotosColumns,
            'order' => $settings->albumPhotosOrder,
            'reverse' => $settings->albumPhotosOrderReverse,
            'caption' => $settings->albumPhotosCaption
        );

        echo '<div id="shashinPhotosForSelectedAlbum">' .$this->handleShortcode($shortcode) . '</div>';
        die();
    }

    public function addShashinMediaButton() {
        global $post_ID, $temp_ID;
        $iframeId = (int) (0 == $post_ID ? $temp_ID : $post_ID);

        $photoBrowserUrl = 'media-upload.php?post_id='
            . $iframeId
            . '&amp;type=shashin&amp;tab=shashin_photos&amp;TB_iframe=true';
        $title = __('Add Shashin photos', 'shashin');
        $imageUrl = plugins_url('Admin/Display/images/', __FILE__) .'picasa.gif';
        $markup = '<a href="%s" class="thickbox" title="%s"><img src="%s" alt="%s"></a>';
        printf($markup, $photoBrowserUrl, $title, $imageUrl, $title);
        return true;
    }

    public function initPhotoMediaMenu() {
        add_action('admin_print_styles', array($this, 'displayMediaMenuCss'));
        wp_iframe(array($this, 'displayPhotoMediaMenu'));
    }

    public function initAlbumMediaMenu() {
        add_action('admin_print_styles', array($this, 'displayMediaMenuCss'));
        wp_iframe(array($this, 'displayAlbumMediaMenu'));
    }

    public function displayMediaMenuCss() {
        $filename = array_shift(explode('?', basename($_SERVER['REQUEST_URI'])));
        if ($filename == 'media-upload.php' && strstr($_SERVER['REQUEST_URI'], 'type=shashin')) {
            wp_admin_css('css/media');
        }

        $cssUrl = plugins_url('Admin/Display/', __FILE__) .'menuMedia.css';
        wp_enqueue_style('shashinMediaMenuStyle', $cssUrl, false, $this->version);
    }

    public function displayPhotoMediaMenu() {
        $this->displayMediaMenu('Admin/Display/menuMediaPhotos.php');
    }

    public function displayAlbumMediaMenu() {
        $this->displayMediaMenu('Admin/Display/menuMediaAlbums.php');
    }

    private function displayMediaMenu($templatePath) {
        add_filter('media_upload_tabs', array($this, 'addMediaMenuTabs'));
        media_upload_header();
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $rawShortcode = array('type' => 'album', 'order' => 'date', 'reverse' => 'y');
        $shortcode = $publicContainer->getShortcode($rawShortcode);
        $albumCollection = $publicContainer->getClonableAlbumCollection();
        $albumCollection->setNoLimit(true);
        $albums = $albumCollection->getCollectionForShortcode($shortcode);
        $loaderUrl = plugins_url('Admin/Display/', __FILE__) .'loader.gif';
        require_once $templatePath;
    }

    public function addMediaMenuTabs() {
        return array(
            'shashin_photos' => __('Photos', 'shashin'),
            'shashin_albums' => __('Albums', 'shashin')
        );
    }

    public function ajaxGetPhotosForMediaMenu() {
        $rawShortcode = array('limit' => 32);

        if ($_REQUEST['shashinAlbumId'] && is_numeric($_REQUEST['shashinAlbumId'])) {
            $rawShortcode['id'] = $_REQUEST['shashinAlbumId'];
            $rawShortcode['type'] = 'albumphotos';
        }

        else {
            $rawShortcode['type'] = 'photo';
        }

        if ($_REQUEST['shashinOrder'] && is_string($_REQUEST['shashinOrder'])) {
            $rawShortcode['order'] = $_REQUEST['shashinOrder'];
        }

        if ($_REQUEST['shashinReverse'] && is_string($_REQUEST['shashinReverse'])) {
            $rawShortcode['reverse'] = $_REQUEST['shashinReverse'];
        }

        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $shortcode = $publicContainer->getShortcode($rawShortcode);
        $photoCollection = $publicContainer->getClonablePhotoCollection();
        $photoCount = $photoCollection->getCountForShortcode($shortcode);
        $totalPages = ceil($photoCount / 32);
        $page = (isset($_REQUEST['shashinPage']) && is_numeric($_REQUEST['shashinPage'])) ? $_REQUEST['shashinPage'] : 1;
        $rawShortcode['limit'] = 32;
        $rawShortcode['offset'] = ($page - 1) * 32;
        $shortcode = $publicContainer->getShortcode($rawShortcode);
        $photoCollection = $publicContainer->getClonablePhotoCollection();
        $collection = $photoCollection->getCollectionForShortcode($shortcode);
        $photos = array();
        foreach ($collection as $photo) {
            $photos[] = array(
                'id' => $photo->id,
                'description' => $photo->description,
                'contentUrl' => $photo->contentUrl,
            );
        }
        echo json_encode(array(
            'photos' => $photos,
            'totalPages' => $totalPages,
            'page' => $page
        ));
        exit;
    }
}

