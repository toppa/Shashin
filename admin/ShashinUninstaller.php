<?php

class Admin_ShashinUninstaller {
    private $dbFacade;
    private $settings;
    private $album;
    private $photo;

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

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function run() {
        try {
            $status = $this->functionsFacade->callFunctionForNetworkSites(array($this, 'runForNetworkSites'), false);
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return $status;
    }

    public function runForNetworkSites() {
        $this->dropTables();
        $this->deleteSettings();
        return true;
    }

    public function dropTables() {
        $albumTable = $this->dbFacade->getTableNamePrefix() . $this->album->getBaseTableName();
        $albumRefData = $this->album->getRefData();
        $this->dbFacade->dropTable($albumTable);

        if ($this->dbFacade->verifyTableExists($albumTable, $albumRefData)) {
            throw new Exception(__('Failed to drop table ', 'shashin') . $albumTable);
        }

        $photoTable = $this->dbFacade->getTableNamePrefix() . $this->photo->getBaseTableName();
        $photoRefData = $this->photo->getRefData();
        $this->dbFacade->dropTable($photoTable);

        if ($this->dbFacade->verifyTableExists($photoTable, $photoRefData)) {
            throw new Exception(__('Failed to drop table ', 'shashin') . $photoTable);
        }

        return true;
    }

    public function deleteSettings() {
        return $this->settings->delete();
    }
}