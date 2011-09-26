<?php
/*
Plugin Name: Shashin (v3 alpha)
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa photos in WordPress.
Author: Michael Toppa
Version: 3.0
Author URI: http://www.toppa.com
*/

$shashinAutoLoaderPath = dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';
register_activation_hook(__FILE__, 'shashinActivate');
load_plugin_textdomain('shashin', false, basename(dirname(__FILE__)) . '/Languages/');

if (file_exists($shashinAutoLoaderPath)) {
    require_once($shashinAutoLoaderPath);
    $shashinToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $shashinAutoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
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
        $shashinAutoLoader = new ToppaAutoLoaderWp('/shashin3alpha');
        $shashin = new ShashinWp($shashinAutoLoader);
        $shashin->install();
    }
}

function shashinCancelActivation($message) {
    deactivate_plugins(basename(__FILE__));
    wp_die($message);
}