<?php

class Admin_ShashinUninstaller {
    private $dbFacade;
    private $settings;
    private $album;
    private $photo;

    public function __construct($dbFacade, $album, $photo, $settings) {
        $this->dbFacade = $dbFacade;
        $this->album = $album;
        $this->photo = $photo;
        $this->settings = $settings;
    }

    public function run() {
        try {
            $this->dropTables();
            $this->deleteSettings();
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function dropTables() {
        $albumTable = $this->album->getTableName();
        $albumRefData = $this->album->getRefData();
        $this->dbFacade->dropTable($albumTable);

        if ($this->dbFacade->verifyTableExists($albumTable, $albumRefData)) {
            throw new Exception(__('Failed to drop table ', 'shashin') . $albumTable);
        }

        $photoTable = $this->photo->getTableName();
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