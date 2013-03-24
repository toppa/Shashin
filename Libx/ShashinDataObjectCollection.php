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
    protected $sqlConditions;
    protected $collection = array();

    public function __construct() {
    }

    public function setDbFacade(Lib_ShashinDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
        return $this->dbFacade;
    }

    public function setClonableDataObject(Lib_ShashinDataObject $clonableDataObject) {
        $this->clonableDataObject = $clonableDataObject;
        return $this->clonableDataObject;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
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
        return $this->clonableDataObject->getTableName();
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
        $this->setIdString();
        $this->setLimitClause();
        $this->setSort();
        $this->setOrderBy();
        $this->setOrderByClause();
        $this->setWhereClause();
        $this->setSqlConditions();
        return true;
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

        elseif ($this->shortcode->limit || $this->shortcode->offset) {
            $this->limitClause = " limit ";
            $this->limitClause .= ($this->shortcode->offset) ? ($this->shortcode->offset . ',') : '';
            $this->limitClause .= ($this->shortcode->limit) ? $this->shortcode->limit : '';
                    }

        elseif (!$this->idString) {
            $this->limitClause = " limit " . $this->settings->defaultPhotoLimit;
        }

        return $this->limitClause;
    }

    public function setSort() {
        if ($this->shortcode->reverse == 'y') {
            $this->sort ='desc';
        }

        else {
            $this->sort = 'asc';
        }

        return $this->sort;
    }

    abstract public function setOrderBy();

    public function setOrderByClause() {
        if ($this->orderBy != 'user') {
            $this->orderByClause = "order by " . $this->orderBy . " " . $this->sort;
        }

        return $this->orderByClause;
    }

    public function setWhereClause() {
        if ($this->shortcode->type == 'albumphotos') {
            $this->whereClause = 'where albumId in (' . $this->idString . ')';
        }

        elseif ($this->idString) {
            $this->whereClause = 'where id in (' . $this->idString . ')';
        }

        if ($this->shortcode->order == 'random') {
            if ($this->whereClause) {
                 $this->whereClause .= ' and';
            }

            else {
                $this->whereClause = 'where';
            }

            $this->whereClause .= ' includeInRandom = "Y"';
        }

        return $this->whereClause;
    }

    public function setSqlConditions() {
        $this->sqlConditions =
            $this->whereClause
            . " " . $this->orderByClause
            . " " . $this->limitClause;
        return $this->sqlConditions;
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
        array_walk($ids, array('Lib_ShashinFunctions', 'trimCallback'));

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
        $rows = $this->dbFacade->sqlSelectMultipleRows(
            $this->getTableName(),
            null,
            null,
            $this->sqlConditions
        );

        return $rows;
    }

    public function getCountForShortcode(Public_ShashinShortcode $shortcode) {
        $this->collection = array(); // make sure we're empty
        $this->setShortcode($shortcode);
        $this->setProperties();
        $row = $this->dbFacade->sqlSelectRow(
            $this->getTableName(),
            array('count(id) as count'),
            null,
            $this->sqlConditions
        );

        return $row['count'];
    }
}
