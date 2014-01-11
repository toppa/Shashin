<?php

class Admin_ShashinUpgradeWp {
    private $dbFacade;
    private $album;
    private $photo;
    private $functionsFacade;
    private $adminContainer;
    private $albumTable;
    private $albumTableBackup;
    private $photoTable;
    private $photoTableBackup;
    private $picasaServer;

    public function __construct() {
    }

    public function setDbFacade(Lib_ShashinDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
        return $this->dbFacade;
    }

    public function setAlbum(Lib_ShashinAlbum $album) {
        $this->album = $album;
        return $this->album;
    }

    public function setPhoto(Lib_ShashinPhoto $photo) {
        $this->photo = $photo;
        return $this->photo;
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setAdminContainer(Admin_ShashinContainer $adminContainer) {
        $this->adminContainer = $adminContainer;
        return $this->adminContainer;
    }

    public function run() {
        $this->setTableProperties();

        if (!$this->isUpgradeNeeded()) {
            return true;
        }

        $this->picasaServer = $this->setPicasaServer();

        if (!$this->picasaServer) {
            throw New Exception(__('Shashin upgrade is needed, but is not possible because the Shashin 2 Picasa server settings are missing. Please re-install Shashin 2 before upgrading to Shashin 3', 'shashin'));
        }

        $this->upgradePhotoTableIfNeeded();
        return $this->upgradeAlbumTableIfNeeded();
    }

    public function setTableProperties() {
        $this->albumTable = $this->album->getTableName();
        $this->albumTableBackup = $this->albumTable . '_shashin2_backup';
        $this->photoTable = $this->photo->getTableName();
        $this->photoTableBackup = $this->photoTable . '_shashin2_backup';
        return true;
    }

    public function isUpgradeNeeded() {
        $photoTableNeedsUpgrade = $this->checkTableNeedsUpgrade($this->photoTable, 'photo_key');
        $albumTableNeedsUpgrade = $this->checkTableNeedsUpgrade($this->albumTable, 'album_key');

        if ($photoTableNeedsUpgrade || $albumTableNeedsUpgrade) {
            return true;
        }

        return false;
    }

    public function checkTableNeedsUpgrade($tableName, $fieldToCheck) {
        if ($this->dbFacade->executeQuery("show tables like '$tableName'", 'get_var')) {
            $tableDescription = $this->dbFacade->executeQuery("describe $tableName", 'get_results');
        }

        else {
            return false;
        }

        foreach ($tableDescription as $row) {
            if ($row['Field'] == $fieldToCheck) {

                return true;
            }
        }

        return false;
    }

    public function setPicasaServer() {
        $oldSettings = $this->functionsFacade->getSetting('shashin_options');

        if (!$oldSettings) {
            return false;
        }

        elseif (is_string($oldSettings)) {
            $oldSettings = unserialize($oldSettings);
        }

        if (is_array($oldSettings) && $oldSettings['picasa_server']) {
            $this->picasaServer = $oldSettings['picasa_server'];
            return $this->picasaServer;
        }

        return false;
    }

    public function upgradePhotoTableIfNeeded() {
        if (!$this->checkTableNeedsUpgrade($this->photoTable, 'photo_key')) {
            return false;
        }

        if (!$this->checkTableNeedsUpgrade($this->albumTable, 'album_key')) {
            throw New Exception(__('Shashin album table not in correct state for upgrade', 'shashin'));
        }

        $this->dbFacade->executeQuery("create table if not exists {$this->photoTableBackup} select * from {$this->photoTable}");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change photo_key id int unsigned auto_increment");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} add sourceId varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change title filename varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change link_url linkUrl text");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change content_url contentUrl text");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change taken_timestamp takenTimestamp bigint");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change uploaded_timestamp uploadedTimestamp int unsigned");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change include_in_random includeInRandom char(1)");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change enclosure_type contentType varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change picasa_order sourceOrder int unsigned");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} change focal_length focalLength varchar(10)");
        $this->dbFacade->executeQuery("delete from {$this->photoTable} where deleted = 'Y'");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} drop column deleted");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} drop column enclosure_url");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} add albumId smallint unsigned");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} add albumType varchar(20)");
        // this works better than alter table, given the data type change
        $this->dbFacade->executeQuery("update {$this->photoTable} set sourceId = photo_id");

        $albumData = $this->dbFacade->executeQuery('select album_key, album_id from ' . $this->album->getTableName(), 'get_results');

        foreach ($albumData as $data) {
            $this->dbFacade->executeQuery(
                "update {$this->photoTable} set albumId = {$data['album_key']} where album_id = {$data['album_id']}"
            );
        }

        $this->dbFacade->executeQuery("alter table {$this->photoTable} drop column album_id");
        $this->dbFacade->executeQuery("alter table {$this->photoTable} drop column photo_id");
        $this->dbFacade->executeQuery("update {$this->photoTable} set albumType = 'picasa' where albumType is null");
        return true;
    }

    public function upgradeAlbumTableIfNeeded() {
        if (!$this->checkTableNeedsUpgrade($this->albumTable, 'album_key')) {
            return false;
        }

        $this->dbFacade->executeQuery("create table if not exists {$this->albumTableBackup} select * from {$this->albumTable}");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change album_key id smallint unsigned auto_increment");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} add sourceId varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} add albumType varchar(20)");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} add dataUrl varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change link_url linkUrl varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change cover_photo_url coverPhotoUrl varchar(255)");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change last_updated lastSync int unsigned");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change photo_count photoCount smallint unsigned");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change pub_date pubDate int unsigned");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change geo_pos geoPos varchar(25)");
        $this->dbFacade->executeQuery("alter table {$this->albumTable} change include_in_random includeInRandom char(1)");
        // this is better than alter table, given the data type change
        $this->dbFacade->executeQuery("update {$this->albumTable} set sourceId = album_id");

        $albumData = $this->dbFacade->executeQuery("select id, sourceId, user from {$this->albumTable}", 'get_results');

        foreach ($albumData as $data) {
            $albumJsonUrl = $this->picasaServer
                            . '/data/feed/api/user/'
                            . $data['user']
                            . '/albumid/'
                            . $data['sourceId']
                            . '?kind=photo&alt=json';

            $this->dbFacade->executeQuery(
                "update {$this->albumTable} set dataUrl = '$albumJsonUrl' where id = {$data['id']}"
            );
        }

        $this->dbFacade->executeQuery("alter table {$this->albumTable} drop column album_id");
        $this->dbFacade->executeQuery("update {$this->albumTable} set albumType = 'picasa' where albumType is null");
        return true;
    }

    public function cleanup() {
        $this->setTableProperties();
        $this->functionsFacade->deleteSetting('shashin_options');
        $this->dbFacade->executeQuery("drop table {$this->albumTableBackup}");
        $this->dbFacade->executeQuery("drop table {$this->photoTableBackup}");
    }
}
