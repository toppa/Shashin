<?php

class ShashinWp {
    private $version = '3.4.11';

    public function __construct() {
    }

    public function getVersion() {
        return $this->version;
    }

    public function install() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $upgrader = $adminContainer->getUpgrader();
            $upgrader->run();
            $installer = $adminContainer->getInstaller($this->version);
            $status = $installer->run();
            return $status;
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }
    }

    public function run() {
        add_filter('upgrader_pre_install', array($this, 'deactivateHighslide'));
        add_action('admin_init', array($this, 'runtimeUpgrade'));
        add_action('admin_menu', array($this, 'initToolsMenu'));
        add_action('admin_menu', array($this, 'initSettingsMenu'));
        add_action('wp_enqueue_scripts', array($this, 'displayPublicHeadTags'));
        add_shortcode('shashin', array($this, 'handleShortcode'));
        add_action('wp_ajax_nopriv_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('wp_ajax_displayAlbumPhotos', array($this, 'ajaxDisplayAlbumPhotos'));
        add_action('wp_ajax_nopriv_saveAlbumDimensions', array($this, 'ajaxSaveAlbumDimensions'));
        add_action('wp_ajax_saveAlbumDimensions', array($this, 'ajaxSaveAlbumDimensions'));
        add_filter('media_upload_tabs', array($this, 'addMediaMenuTabs'));
        add_action('media_upload_shashinPhotos', array($this, 'initPhotoMediaMenu'));
        add_action('media_upload_shashinAlbums', array($this, 'initAlbumMediaMenu'));
        add_action('wp_ajax_shashinGetPhotosForMediaMenu', array($this, 'ajaxGetPhotosForMediaMenu'));
        $this->scheduleSyncIfNeeded();
        add_action('shashinSync', array($this, 'runScheduledSync'));
        $this->supportOldShortcodesIfNeeded();
        add_action('widgets_init', array($this, 'registerWidget'));
        add_action('admin_head', array($this, 'displayPluginPageUpgradeNag'));
        return true;
    }

    public function deactivateHighslide() {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        if (is_plugin_active('highslide-for-shashin/start.php')) {
            deactivate_plugins(WP_PLUGIN_DIR . '/highslide-for-shashin/start.php');
        }
    }

    public function runtimeUpgrade() {
        try {
            // check if upgrading from Shashin 2
            $adminContainer = new Admin_ShashinContainer();
            $upgrader = $adminContainer->getUpgrader();
            $upgrader->setTableProperties();

            if ($upgrader->isUpgradeNeeded() == true) {
                throw New Exception(__('You must deactivate and reactivate Shashin to upgrade from version 2', 'shashin'));
            }

            $installer = $adminContainer->getInstaller($this->version);
            $status = $installer->runtimeUpgrade();
            return $status;
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }
    }

    public function initToolsMenu() {
        $toolsPage = add_management_page(
            'Shashin',
            'Shashin',
            'edit_posts',
            'ShashinToolsMenu',
            array($this, 'displayToolsMenu')
        );

        // from http://planetozh.com/blog/2008/04/how-to-load-javascript-with-your-wordpress-plugin/
        add_action("admin_print_styles-$toolsPage", array($this, 'displayAdminHeadTags'));
    }

    public function displayToolsMenu() {
        try {
            $adminContainer = new Admin_ShashinContainer();

            if (array_key_exists('shashinMenu', $_REQUEST) && $_REQUEST['shashinMenu'] == 'photos') {
                $menuActionHandler = $adminContainer->getMenuActionHandlerPhotos($_REQUEST['id']);
            }

            else {
                $menuActionHandler = $adminContainer->getMenuActionHandlerAlbums();
            }

            echo $menuActionHandler->run();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function displayAdminHeadTags() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $headTags = $adminContainer->getHeadTags($this->version);
            $headTags->run();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function initSettingsMenu() {
       $optionsPage = add_options_page(
            'Shashin',
            'Shashin',
            'manage_options',
            'shashin',
            array($this, 'displaySettingsMenu')
        );

        add_action("admin_print_styles-$optionsPage", array($this, 'displayAdminHeadTags'));
    }

    public function displaySettingsMenu() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $settingsMenuManager = $adminContainer->getSettingsMenuManager();
            echo $settingsMenuManager->run();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function displayPublicHeadTags() {
        try {
            $publicContainer = new Public_ShashinContainer();
            $headTags = $publicContainer->getHeadTags($this->version);
            $headTags->run();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function handleShortcode($arrayShortcode) {
        try {
            // if the shortcode has no attributes specified, WP passes
            // an empty string instead of an array
            if (!is_array($arrayShortcode)) {
                $arrayShortcode = array();
            }

            $publicContainer = new Public_ShashinContainer();
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
            return $this->formatExceptionMessage($e);
        }
    }

    public function ajaxDisplayAlbumPhotos() {
        try {
            $publicContainer = new Public_ShashinContainer();
            $settings = $publicContainer->getSettings();
            $shortcode = array(
                'type' => 'albumphotos',
                'id' => htmlentities($_REQUEST['shashinAlbumId']),
                'size' => $settings->albumPhotosSize,
                'crop' => $settings->albumPhotosCrop,
                'columns' => $settings->albumPhotosColumns,
                'order' => $settings->albumPhotosOrder,
                'reverse' => $settings->albumPhotosOrderReverse,
                'caption' => $settings->albumPhotosCaption
            );

            echo '<div data-shashinalbum="'
                .  $shortcode['id']
                . '" id="shashinAlbumPhotos_'
                . $shortcode['id']
                . '" style="display: table; '
                . htmlentities($_REQUEST['shashinParentTableStyle'])
                . '">' .$this->handleShortcode($shortcode) . '</div>';
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

        die();
    }

    public function ajaxSaveAlbumDimensions() {
        // 1st: shashinalbum or shashinphoto
        // 2nd: the album or photo id
        // 3rd: the thumbnail width
        // 4th: the thumbnail height
        $thumbnailData = array_chunk($_REQUEST['dimensions'], 4);
        $container = new Lib_ShashinContainer();
        $clonableAlbum = $container->getClonableAlbum();
        $clonablePhoto = $container->getClonablePhoto();

        foreach ($thumbnailData as $data) {
            if ($data[0] == 'shashinalbum') {
                $album = clone $clonableAlbum;
                $album->get($data[1]);
                $album->width = $data[2];
                $album->height = $data[3];
                $album->flush();
            }

            else if ($data[0] == 'shashinphoto') {
                $photo = clone $clonablePhoto;
                $photo->get($data[1]);
                $photo->width = $data[2];
                $photo->height = $data[3];
                $photo->flush();
            }
        }
        die();
    }

    public function addMediaMenuTabs($tabs) {
    	$shashinTab = array(
            'shashinPhotos' => __('Shashin Photos', 'shashin'),
            'shashinAlbums' => __('Shashin Albums', 'shashin')
        );
    	return array_merge($tabs, $shashinTab);
    }

    public function initPhotoMediaMenu() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $mediaMenu = $adminContainer->getMediaMenu($this->version, $_REQUEST);
            $mediaMenu->initPhotoMenu();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function initAlbumMediaMenu() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $mediaMenu = $adminContainer->getMediaMenu($this->version, $_REQUEST);
            $mediaMenu->initAlbumMenu();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }
    }

    public function ajaxGetPhotosForMediaMenu() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $mediaMenu = $adminContainer->getMediaMenu($this->version, $_REQUEST);
            echo $mediaMenu->getPhotosForMenu();
        }

        catch (Exception $e) {
            echo $this->formatExceptionMessage($e);
        }

        exit;
    }

    public function scheduleSyncIfNeeded() {
        try {
            if (!wp_next_scheduled('shashinSync') ) {
                $publicContainer = new Public_ShashinContainer();
                $settings = $publicContainer->getSettings();

                if ($settings->scheduledUpdate == 'y') {
                    wp_schedule_event(time(), 'hourly', 'shashinSync');
                }
            }
        }

        // an Exception is thrown on a first installation because the scheduledUpdate
        // property is not set yet (apparently WP tries to run Shashin before
        // installing it - weird), so suppress it
        catch (Exception $e) {
            return false;
        }
    }

    public function runScheduledSync() {
        try {
            $adminContainer = new Admin_ShashinContainer();
            $scheduledSynchronizer = $adminContainer->getScheduledSynchronizer();
            $scheduledSynchronizer->run();
        }

        // suppress any exceptions since this is running through wp cron
        catch (Exception $e) {
            return false;
        }
    }

    public function supportOldShortcodesIfNeeded() {
        try {
            $libContainer = new Lib_ShashinContainer();
            $settings = $libContainer->getSettings();

            if ($settings->supportOldShortcodes == 'y') {
                // the 0 priority flag gets the shashin div in before the autoformatter
                // can wrap it in a paragraph
                add_filter('the_content', array($this, 'handleOldShortcodes'), 0);
            }
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }
    }

    public function handleOldShortcodes($content) {
        try {
            $publicContainer = new Public_ShashinContainer();
            $oldShortcode = $publicContainer->getOldShortcode($content, $_REQUEST);
            return $oldShortcode->run();
        }

        catch (Exception $e) {
            return $this->formatExceptionMessage($e);
        }
    }

    public function registerWidget() {
        register_widget('Admin_ShashinWidgetWp');
    }

    public function displayPluginPageUpgradeNag() {
        if (strpos($_SERVER['REQUEST_URI'], 'plugins.php')) {
            $libContainer = new Lib_ShashinContainer();
            $functionsFacade = $libContainer->getFunctionsFacade();

            if ($functionsFacade->getSetting('shashin_options')) {
                echo '<div class="updated"><p>';
                echo __('Please go to the Shashin Tools Menu to complete the upgrade.', 'shashin');
                echo '</p></div>' . PHP_EOL;
            }
        }
    }

    public function formatExceptionMessage($e) {
        return '<p><strong>'
            . __('Shashin Error', 'shashin')
            . ':</strong></p><pre>'
            . $e->getMessage()
            . '</pre>';
    }

    public static function display($arrayShortcode) {
        $shashinWp = new ShashinWp();
        return $shashinWp->handleShortcode($arrayShortcode);
    }
}
