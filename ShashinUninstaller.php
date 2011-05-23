<?php

class ShashinUninstaller {
    private $dbFacade;
    private $settings;
    private $albumRef;
    private $photoRef;

    public function __construct(&$dbFacade, &$albumRef, &$photoRef, &$settings) {
        $this->dbFacade = $dbFacade;
        $this->albumRef = $albumRef;
        $this->photoRef = $photoRef;
        $this->settings = $settings;
    }

    public function run() {
        try {
            $this->dropTables();
            $this->settings->deleteSettings();
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function dropTables() {
        $albumTable = $this->albumRef->getTableName();
        $this->dbFacade->dropTable($albumTable);

        if ($this->dbFacade->verifyTableExists($albumTable)) {
            throw new Exception(__('Failed to drop table ', 'shashin') . $albumTable);
        }

        $photoTable = $this->photoRef->getTableName();
        $this->dbFacade->dropTable($photoTable);

        if ($this->dbFacade->verifyTableExists($photoTable)) {
            throw new Exception(__('Failed to drop table ', 'shashin') . $photoTable);
        }

        return true;
    }
}