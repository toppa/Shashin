<?php

abstract class Lib_ShashinDataObjectCollection {
    protected $dbFacade;
    protected $clonableDataObject;
    protected $settings;
    protected $shortcode;
    protected $useThumbnailId = false;
    protected $noLimit = false;
    protected $idString;
    protected $thumbnailSize;
    protected $limitClause;
    protected $orderBy;
    protected $sort;
    protected $orderByClause;
    protected $whereClause;
    protected $collection = array();

    public function __construct() {
    }

    public function setDbFacade(ToppaDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
    }

    public function setClonableDataObject(Lib_ShashinDataObject $clonableDataObject) {
        $this->clonableDataObject = $clonableDataObject;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
    }

    public function setUseThumbnailId($useThumbnailId) {
        if (is_bool($useThumbnailId)) {
            $this->useThumbnailId = $useThumbnailId;
        }

        return $this->useThumbnailId;
    }

    public function setNoLimit($noLimit) {
        if (is_bool($noLimit)) {
            $this->noLimit = $noLimit;
        }

        return $this->noLimit;
    }

    public function getTableName() {
        return  $this->clonableDataObject->getTableName();
    }

    public function getRefData() {
        return $this->clonableDataObject->getRefData();
    }

    public function getCollectionForShortcode(Public_ShashinShortcode $shortcode) {
        $this->collection = array(); // make sure we're empty
        $this->setShortcode($shortcode);
        $this->setProperties();

        if ($this->orderBy == 'user') {
            return $this->getCollectionInUserOrder();
        }

        return $this->getCollection();
    }

    public function setShortcode(Public_ShashinShortcode $shortcode) {
        $this->shortcode = $shortcode;
        return $this->shortcode;
    }

    public function setProperties() {
        if ($this->useThumbnailId == true) {
            $this->setIdString();
        }

        else {
            $this->setIdString();
        }

        $this->setLimitClause();
        $this->setOrderBy();
        $this->setSort();
        $this->setOrderByClause();
        $this->setWhereClause();
    }

    public function setIdString() {
        if ($this->useThumbnailId == true) {
            $this->idString = $this->shortcode->thumbnail;
        }

        else {
            $this->idString = $this->shortcode->id;
        }

        return $this->idString;
    }

    public function setLimitClause() {
        if ($this->noLimit) {
            $this->limitClause = null;
        }

        elseif ($this->shortcode->limit) {
            $this->limitClause = 'limit ' . $this->shortcode->limit;
        }

        elseif (!$this->idString || $this->shortcode->type == 'albumphotos') {
            $this->limitClause = 'limit ' . $this->settings->photosPerPage;
        }

        return $this->limitClause;
    }

    abstract public function setOrderBy();

    public function setSort() {
        if ($this->shortcode->reverse == 'y') {
            $this->sort ='desc';
        }

        else {
            $this->sort = 'asc';
        }

        return $this->sort;
    }

    public function setOrderByClause() {
        if ($this->orderBy != 'user') {
            $this->orderByClause = "order by " . $this->orderBy . " " . $this->sort;
        }

        return $this->orderByClause;
    }

    public function setWhereClause() {
        if ($this->shortcode->type == 'albumphotos') {
            $this->whereClause = "where albumId in (" . $this->idString . ")";
        }

        elseif ($this->idString) {
            $this->whereClause = "where id in (" . $this->idString . ")";
        }

        return $this->whereClause;
    }

    public function getCollection() {
        $rows = $this->getData();

        if (!is_array($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            $dataObject = clone $this->clonableDataObject;
            $dataObject->set($row);
            $this->collection[] = $dataObject;
        }

        return $this->collection;
    }

    public function getCollectionInUserOrder() {
        $rows = $this->getData();

        if (!is_array($rows)) {
            return null;
        }

        $ids = explode(",", $this->idString);
        array_walk($ids, array('ToppaFunctions', 'trimCallback'));

        foreach ($ids as $id) {
            foreach ($rows as $row) {
                if ($id == $row['id']) {
                    $dataObject = clone $this->clonableDataObject;
                    $dataObject->set($row);
                    $this->collection[] = $dataObject;
                }
            }
        }

        return $this->collection;
    }

    public function getData() {
        $sqlConditions =
                $this->whereClause
                . " " . $this->orderByClause
                . " " . $this->limitClause;
        $rows = $this->dbFacade->sqlSelectMultipleRows(
            $this->getTableName(),
            null,
            null,
            $sqlConditions
        );

        return $rows;
    }
}
