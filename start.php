<?php
/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating photos and videos from Picasa, YouTube, and Twitpic in WordPress.
Author: Michael Toppa
Version: 3.2.3
Author URI: http://www.toppa.com
License: GPLv2 or later
*/
$shashinAutoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
add_action('wpmu_new_blog', 'shashinActivateForNewNetworkSite');
register_activation_hook(__FILE__, 'shashinActivate');
register_deactivation_hook(__FILE__, 'shashinDeactivateForNetworkSites');
load_plugin_textdomain('shashin', false, basename(dirname(__FILE__)) . '/Languages/');

if (file_exists($shashinAutoLoaderPath)) {
    require_once($shashinAutoLoaderPath);
    new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    new ToppaAutoLoaderWp('/shashin');
    $shashin = new ShashinWp();
    $shashin->run();
}

function shashinActivateForNewNetworkSite($blog_id) {
    global $wpdb;

    if (is_plugin_active_for_network(__FILE__)) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        shashinActivate();
        switch_to_blog($old_blog);
    }
}

function shashinActivate() {
    $status = shashinActivationChecks();

    if (is_string($status)) {
        shashinCancelActivation($status);
        return null;
    }

    require_once dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
    new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    new ToppaAutoLoaderWp('/shashin');
    $shashin = new ShashinWp();
    $status = $shashin->install();

    if (is_string($status)) {
        shashinCancelActivation($status);
        return null;
    }

    return null;
}

function shashinActivationChecks() {
    $autoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
    $toppaLibsVersion = get_option('toppaLibsVersion');

    if (!file_exists($autoLoaderPath) || !$toppaLibsVersion || version_compare($toppaLibsVersion, '1.3.5', '<')) {
        return __('To activate Shashin you need to have the current version of', 'shashin')
            . ' <a href="plugin-install.php?tab=plugin-information&plugin=toppa-plugin-libraries-for-wordpress">Toppa Plugins Libraries for WordPress</a>. '
            . __('Click this link to view details, and then click the "Install Now" button to get the current version. Then you can activate Shashin.', 'shashin');
    }

    if (!function_exists('spl_autoload_register')) {
        return __('Shashin not activated. You must have at least PHP 5.1.2 to use Shashin', 'shashin');
    }

    if (version_compare(get_bloginfo('version'), '3.0', '<')) {
        return __('Shashin not activated. You must have at least WordPress 3.0 to use Shashin', 'shashin');
    }

    return true;
}

function shashinCancelActivation($message) {
    // deactivate without calling the deactivatation hook, as it relies
    // on toppa-libs, which may not be available or may be an old version
    deactivate_plugins('shashin/start.php', true);
    wp_die($message);
}

function shashinDeactivateForNetworkSites() {
    $functionsFacade = new ToppaFunctionsFacadeWp();
    $functionsFacade->callFunctionForNetworkSites('shashinDeactivate');
}

function shashinDeactivate() {
    wp_clear_scheduled_hook('shashinSync');
}
