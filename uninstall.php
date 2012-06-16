<?php

if (!defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once(dirname(__FILE__) . '/../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');
new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
new ToppaAutoLoaderWp('/shashin');
$shashinAdminContainer = new Admin_ShashinContainer();
$shashinUninstaller = $shashinAdminContainer->getUninstaller();
$shashinUninstallStatus = $shashinUninstaller->run();

if ($shashinUninstallStatus !== true) {
    wp_die(__('Uninstall failed: ', 'shashin') . $shashinUninstallStatus);
}
