<?php

abstract class Lib_ShashinDataObjectCollection {
    protected $dbFacade;
    protected $clonableDataObject;
    protected $settings;
    protected $request;
    protected $shortcode;
    protected $useThumbnailId = false;
    protected $noLimit = false;
    protected $mayNeedPagination = false;
    protected $idString;
    protected $thumbnailSize;
    protected $limitClause;
    protected $orderBy;
    protected $sort;
    protected $orderByClause;
    protected $whereClause;
    protected $sqlConditions;
    protected $collection = array();
    protected $count;

    public function __construct() {
    }

    public function setDbFacade(ToppaDatabaseFacade $dbFacade) {
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

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
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

        if ($this->mayNeedPagination) {
            $this->setCount();
        }

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
        $this->setMayNeedPagination();
        $this->setLimitClause();
        $this->setOrderBy();
        $this->setSort();
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

    public function setMayNeedPagination() {
        if (!$this->idString || $this->shortcode->type == 'albumphotos') {
            $this->mayNeedPagination = true;
        }

        else {
            $this->mayNeedPagination = false;
        }

        return $this->mayNeedPagination;
    }

    public function getMayNeedPagination() {
        return $this->mayNeedPagination;
    }

    public function setLimitClause() {
        if ($this->noLimit) {
            $this->limitClause = null;
        }

        elseif ($this->shortcode->limit) {
            $this->limitClause = " limit " . $this->shortcode->limit;
        }

        elseif ($this->mayNeedPagination) {
            $limit = $this->settings->photosPerPage;
            $page = is_numeric($this->request['shashinPage']) ? $this->request['shashinPage'] : 1;
            $currentSet = ($page - 1) * $limit;
            $this->limitClause .= " limit $limit offset $currentSet";
        }

        else {
            $this->limitClause = null;
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

    public function setSqlConditions() {
        $this->sqlConditions =
            $this->whereClause
            . " " . $this->orderByClause
            . " " . $this->limitClause;

        return $this->sqlConditions;
    }

    public function setCount() {
        $result = $this->dbFacade->sqlSelectRow(
            $this->getTableName(),
            array('count(id) as count'),
            null,
            $this->sqlConditions
        );

        $this->count = $result['count'];
        return $this->count;
    }

    public function getCount() {
        return $this->count;
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
        $rows = $this->dbFacade->sqlSelectMultipleRows(
            $this->getTableName(),
            null,
            null,
            $this->sqlConditions
        );

        return $rows;
    }
}
