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
        add_action('template_redirect', array($this, 'displayPublicHeadTags'));
        add_shortcode('shashin', array($this, 'handleShortcode'));
        add_action('wp_ajax_nopriv_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('wp_ajax_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('media_buttons', array($this, 'addMediaButton'), 20);
        add_action('media_upload_shashin_photos', array($this, 'initPhotoMediaMenu'));
        add_action('media_upload_shashin_albums', array($this, 'initAlbumMediaMenu'));
        add_action('wp_ajax_shashinGetPhotosForMediaMenu', array($this, 'ajaxGetPhotosForMediaMenu'));
        $this->scheduleSyncIfNeeded();
        add_action('shashinSync', array($this, 'runScheduledSync'));
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
        //add_action("admin_print_styles-$toolsPage", array($this, 'displayToolsMenuJsAndCss'));
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

    /*
    public function displayToolsMenuJsAndCss() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $headTags = $adminContainer->getHeadTags();
        $cssUrl = $headTags->getCssUrl();
        wp_enqueue_style('shashinAdminStyle', $cssUrl, false, $this->version);
        $jsUrl = $headTags->getJsUrl();
        wp_enqueue_script('shashinAdminScript', $jsUrl, array('jquery'), $this->version);
        $menuDisplayUrl = $headTags->getMenuDisplayUrl();
        wp_localize_script('shashinAdminScript', 'shashinDisplay', array('url' => $menuDisplayUrl));
    }

    */
    public function displayPublicHeadTags() {
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $headTags = $publicContainer->getHeadTags($this->version);
        $headTags->run();
    }

    public function handleShortcode($arrayShortcode) {
        try {
            // if the shortcode has no attributes specified, WP passes
            // an empty string instead of an array
            if (!is_array($arrayShortcode)) {
                $arrayShortcode = array();
            }

            $publicContainer = new Public_ShashinContainer($this->autoLoader);
            $shortcode = $publicContainer->getShortcode($arrayShortcode);

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

    public function addMediaButton() {
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
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $mediaMenu = $adminContainer->getMediaMenu($this->version, $_REQUEST);
        $mediaMenu->displayPhotoMenu();
    }

    public function initAlbumMediaMenu() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $mediaMenu = $adminContainer->getMediaMenu($this->version, $_REQUEST);
        $mediaMenu->displayAlbumMenu();
    }

    public function ajaxGetPhotosForMediaMenu() {
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $mediaMenu = $adminContainer->getMediaMenu($this->version, $_REQUEST);
        echo $mediaMenu->getPhotosForMenu();
        exit;
    }

    public function scheduleSyncIfNeeded() {
        if (!wp_next_scheduled('shashinSync') ) {
            $publicContainer = new Public_ShashinContainer($this->autoLoader);
            $settings = $publicContainer->getSettings();

            if ($settings->scheduledUpdate == 'y') {
                wp_schedule_event(time(), 'hourly', 'shashinSync');
            }
        }
    }

    public function runScheduledSync() {
        $arrayShortcode = array('type' => 'album', 'order' => 'sync');
        $publicContainer = new Public_ShashinContainer($this->autoLoader);
        $shortcode = $publicContainer->getShortcode($arrayShortcode);
        $albumsToCount = $publicContainer->getClonableAlbumCollection();
        $albumsToCount->setNoLimit(true);
        $albumCount = $albumsToCount->getCountForShortcode($shortcode);
        $oldestTenPercent = ceil($albumCount / 10);
        $arrayShortcode['limit'] = $oldestTenPercent;
        $shortcode = $publicContainer->getShortcode($arrayShortcode);
        $albumsToSync = $publicContainer->getClonableAlbumCollection();
        $albumsToSync->getCollectionForShortcode($shortcode);
        $adminContainer = new Admin_ShashinContainer($this->autoLoader);
        $albumHandler = $adminContainer->getMenuActionHandlerAlbums();

        foreach ($albumsToSync as $album) {
            $albumHandler->runSynchronizerForExistingAlbum($album);
        }
    }

    public function supportOldShortcodesIfNeeded() {
        $publicContainer = new Lib_ShashinContainer($this->autoLoader);
        $settings = $publicContainer->getSettings();

        if ($settings->supportOldShortcodes == 'y') {
            // the 0 priority flag gets the shashin div in before the autoformatter
            // can wrap it in a paragraph
            add_filter('the_content', array($this, 'handleOldShortcodes'), 0);
        }
    }

    public function handleOldShortcodes($content) {
        $publicContainer = new Lib_ShashinContainer($this->autoLoader);
        $oldShortcode = $publicContainer->getOldShortcode($content);
        return $oldShortcode->run();
    }
}
