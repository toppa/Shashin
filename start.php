<?php
/*
Plugin Name: Shashin
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating photos and videos from Picasa, YouTube, and Twitpic in WordPress.
Author: Michael Toppa
Version: 3.0.9
Author URI: http://www.toppa.com
*/


$shashinAutoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
register_activation_hook(__FILE__, 'shashinActivate');
register_deactivation_hook(__FILE__, 'shashinDeactivate');
load_plugin_textdomain('shashin', false, basename(dirname(__FILE__)) . '/Languages/');

if (file_exists($shashinAutoLoaderPath)) {
    require_once($shashinAutoLoaderPath);
    $shashinToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $shashinAutoLoader = new ToppaAutoLoaderWp('/shashin');
    $shashin = new ShashinWp($shashinAutoLoader);
    $shashin->run();
}

function shashinActivate() {
    $autoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

    if (!file_exists($autoLoaderPath)) {
        $message = __('To activate Shashin you need to first install', 'shashin')
            . ' <a href="http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/">Toppa Plugins Libraries for WordPress</a>';
        shashinCancelActivation($message);
    }

    elseif (!function_exists('spl_autoload_register')) {
        shashinCancelActivation(__('You must have at least PHP 5.1.2 to use Shashin', 'shashin'));
    }

    elseif (version_compare(get_bloginfo('version'), '3.0', '<')) {
        shashinCancelActivation(__('You must have at least WordPress 3.0 to use Shashin', 'shashin'));
    }

    else {
        require_once($autoLoaderPath);
        $toppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
        $shashinAutoLoader = new ToppaAutoLoaderWp('/shashin');
        $shashin = new ShashinWp($shashinAutoLoader);
        $status = $shashin->install();

        if (is_string($status)) {
            shashinCancelActivation($status);
        }
    }

}

function shashinCancelActivation($message) {
    deactivate_plugins(basename(__FILE__));
    wp_die($message);
}

function shashinDeactivate() {
    wp_clear_scheduled_hook('shashinSync');
}
