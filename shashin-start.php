<?php
/*
Plugin Name: Shashin (v3 alpha)
Plugin URI: http://www.toppa.com/shashin-wordpress-plugin/
Description: A plugin for integrating Picasa, Twitpic, and Flickr photos in WordPress.
Author: Michael Toppa
Version: 3.0
Author URI: http://www.toppa.com
*/

define(SHASHIN_DIR, dirname(__FILE__));

//register_activation_hook(__FILE__, 'shashinActivate');

function shashinActivate() {
    require_once(SHASHIN_DIR . '/Admin/ShashinAdminContainer.php');
    $installer = ShashinAdminContainer::makeInstaller();
    $activationStatus = $installer->run();

    if ($activationStatus !== true) {
        // trigger_error is how you indicate an activation problem in WordPress
        trigger_error(__('Activation failed: ', 'shashin') . $activationStatus, E_USER_ERROR);
    }
}

/*
require_once(dirname(__FILE__) . '/../toppa-libs/ToppaFunctions.php');
require_once(ToppaFunctions::path() . '/ToppaWpFunctionsFacade.php');
require_once(ToppaFunctions::path() . '/ToppaWpHooksFacade.php');
require_once(ToppaFunctions::path() . '/ToppaWpDatabaseFacade.php');
require_once('ShashinSettings.php');
require_once('Shashin.php');


$hooksFacade = new ToppaWpHooksFacade();
$functionsFacade = new ToppaWpFunctionsFacade();
$dbFacade = new ToppaWpDatabaseFacade();
$shashinSettings = new ShashinSettings($dbFacade);
$shashin = new Shashin3Alpha($hooksFacade, $functionsFacade, $dbFacade, $shashinSettings);
$shashin->run();

// WordPress requires the activation function to be in the main plugin file (this one)
register_activation_hook(__FILE__, 'shashinActivate');

function shashinActivate() {
    $hooksFacade = new ToppaWpHooksFacade();
    $functionsFacade = new ToppaWpFunctionsFacade();
    $dbFacade = new ToppaWpDatabaseFacade();
    $shashinSettings = new ShashinSettings($dbFacade);
    $shashin = new Shashin3Alpha($hooksFacade, $functionsFacade, $dbFacade, $shashinSettings);
    $activationStatus = $shashin->install();

    if ($activationStatus !== true) {
        // trigger_error is how you indicate an activation problem in WordPress
        trigger_error(__('Activation failed: ', 'shashin') . $activationStatus, E_USER_ERROR);
    }
}


 */