<?php

require_once(SHASHIN_DIR . '/Lib/ToppaWpDatabaseFacade.php');
require_once(SHASHIN_DIR . '/Lib/ShashinPhoto.php');
require_once(SHASHIN_DIR . '/Lib/ShashinAlbum.php');
//require_once(SHASHIN_DIR . '/Lib/ShashinSettings.php');
//require_once(SHASHIN_DIR . '/Lib/ShashinInstaller.php');

class ShashinAdminContainer {
    public static function makeInstaller() {
        $dbFacade = new ToppaWpDatabaseFacade();
        $album = new ShashinAlbum($dbFacade);
        $photo = new ShashinPhoto($dbFacade);
        $settings = new ShashinSettings($dbFacade);
        $installer = new ShashinInstaller($dbFacade, $album, $photo, $settings);
        return $installer;
    }
}
