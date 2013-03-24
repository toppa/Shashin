<?php

if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$shashinPath = dirname(__FILE__);
$shashinParentDir = basename($shashinPath);
$shashinAutoLoaderPath = $shashinPath . '/lib/ShashinAutoLoader.php';

require_once($shashinAutoLoaderPath);
new ShashinAutoLoader('/shashin');
$shashinAdminContainer = new Admin_ShashinContainer();
$shashinUninstaller = $shashinAdminContainer->getUninstaller();
$shashinUninstallStatus = $shashinUninstaller->run();

if ($shashinUninstallStatus !== true) {
    wp_die(__('Uninstall failed: ', 'shashin') . $shashinUninstallStatus);
}
