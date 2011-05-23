<?php

if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

//require_once(dirname(__FILE__) . '/../toppa-libs/ToppaFunctions.php');
//require_once(ToppaFunctions::path() . '/ToppaWpDatabaseFacade.php');
require_once('ShashinSettings.php');
require_once('ShashinUninstaller.php');
require_once('ShashinPhotoRef.php');
require_once('ShashinAlbumRef.php');

$dbFacade = new ToppaWpDatabaseFacade();
$shashinSettings = new ShashinSettings($dbFacade);
$albumRef = new ShashinAlbumRef($dbFacade);
$photoRef = new ShashinPhotoRef($dbFacade);
$shashinUninstaller = new ShashinUninstaller($dbFacade, $albumRef, $photoRef, $shashinSettings);
$uninstallStatus = $shashinUninstaller->run();

if ($uninstallStatus !== true) {
    trigger_error(__('Uninstall failed: ', 'shashin') . $uninstallStatus, E_USER_ERROR);
}

