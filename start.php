<?php
/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating photos and videos from Picasa, YouTube, and Twitpic in WordPress.
Author: Michael Toppa
Version: 3.4.11
Author URI: http://www.toppa.com
License: GPLv2 or later
*/

$shashinPath = dirname(__FILE__);
$shashinParentDir = basename($shashinPath);
$shashinAutoLoaderPath = $shashinPath . '/lib/ShashinAutoLoader.php';

add_action('wpmu_new_blog', 'shashinActivateForNewNetworkSite');
register_activation_hook(__FILE__, 'shashinActivate');
register_deactivation_hook(__FILE__, 'shashinDeactivateForNetworkSites');
load_plugin_textdomain('shashin', false, $shashinParentDir . '/languages/');

if (file_exists($shashinAutoLoaderPath)) {
    require_once($shashinAutoLoaderPath);
    new ShashinAutoLoader('/shashin');
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

    $shashin = new ShashinWp();
    $status = $shashin->install();

    if (is_string($status)) {
        shashinCancelActivation($status);
        return null;
    }

    return null;
}

function shashinActivationChecks() {
    if (!function_exists('spl_autoload_register')) {
        return __('Shashin not activated. You must have at least PHP 5.1.2 to use Shashin', 'shashin');
    }

    if (version_compare(get_bloginfo('version'), '3.0', '<')) {
        return __('Shashin not activated. You must have at least WordPress 3.0 to use Shashin', 'shashin');
    }

    return true;
}

function shashinCancelActivation($message) {
    deactivate_plugins(__FILE__);
    wp_die($message);
}

function shashinDeactivateForNetworkSites() {
    $functionsFacade = new Lib_ShashinFunctionsFacade();
    $functionsFacade->callFunctionForNetworkSites('shashinDeactivate');
}

function shashinDeactivate() {
    wp_clear_scheduled_hook('shashinSync');
}
