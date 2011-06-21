<?php

class Lib_ShashinAlbumCollection extends Lib_ShashinDataObjectCollection {
    private $clonableAlbum;
    private $albumTableName;

    public function __construct($dbFacade, $clonableAlbum) {
        $this->clonableAlbum = $clonableAlbum;
        $this->albumTableName = $this->clonableAlbum->getTableName();
        parent::__construct($dbFacade);
    }

    public function getCollection() {
        $rows = $this->getData();

        foreach ($rows as $row) {
            $album = clone $this->clonableAlbum;
            $album->set($row);
            $this->collection[$album->albumKey] = $album;
        }

        return $this->collection;
    }

    public function getData() {
        $this->setOrderByClause();
        $rows = $this->dbFacade->sqlSelectMultipleRows($this->albumTableName, null, $this->whereClause, $this->orderByClause);

        if (empty($rows)) {
            throw New Exception(__("Failed to find database record for albums", "shashin"));
        }

        return $rows;
    }

    public function getHtmlForShortcode() {

        if ($this->keysString) {
            $this->whereClause = "where albumKey in (" . $this->keysString . ")";
        }

        $this->getCollection();
    }
}
