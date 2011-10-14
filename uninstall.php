<?php

if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once(dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');

$shashinAutoLoader = new ToppaAutoLoaderWp('/shashin');
$shashinAdminContainer = new Admin_ShashinContainer($shashinAutoLoader);
$shashinUninstaller = $shashinAdminContainer->getUninstaller();
$shashinUninstallStatus = $shashinUninstaller->run();

if ($shashinUninstallStatus !== true) {
    trigger_error(__('Uninstall failed: ', 'shashin') . $shashinUninstallStatus, E_USER_ERROR);
}
