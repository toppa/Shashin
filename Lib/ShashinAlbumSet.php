<?php

class Lib_ShashinAlbumSet {
    private $dbFacade;
    private $clonableAlbum;
    private $tableName;
    private $albumSet = array();

    public function __construct(&$dbFacade, &$clonableAlbum) {
        $this->dbFacade = $dbFacade;
        $this->clonableAlbum = $clonableAlbum;
        $this->tableName = $this->clonableAlbum->getTableName();
    }

    public function getAllAlbums($orderByClause = null) {
        $albumData = $this->dbFacade->sqlSelectMultipleRows($this->tableName, null, null, $orderByClause);

        foreach ($albumData as $data) {
            $album = clone $this->clonableAlbum;
            $album->set($data);
            $this->albumSet[$album->albumKey] = $album;
        }

        return $this->albumSet;
    }
}
