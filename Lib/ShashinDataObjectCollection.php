<?php

abstract class Lib_ShashinDataObjectCollection {
    protected $defaultLimit = 9;
    protected $limitNeeded = true;
    protected $dbFacade;
    protected $clonableDataObject;
    protected $tableName;
    protected $idString;
    protected $thumbnailSize;
    protected $limitClause;
    protected $orderBy;
    protected $sort;
    protected $orderByClause;
    protected $whereClause;
    protected $useThumbnailId = false;
    protected $collection = array();
    // fields not part of the db query don't belong in this class
    protected $validInputValues = array(
        'size' => array(null, 'x-small', 'small', 'medium', 'large', 'max'),
        'format' => array(null, 'table', 'list'),
        'caption' => array(null, 'y', 'n', 'c'),
        'order' => array(null, 'id', 'date', 'filename', 'title', 'location', 'count', 'sync', 'random', 'source', 'user'),
        'reverse' => array(null, 'y', 'n'),
        'position' => array(null, 'left', 'right', 'none', 'inherit', 'center'),
        'clear' => array(null, 'left', 'right', 'none', 'both', 'inherit')
    );

    public function __construct(ToppaDatabaseFacade $dbFacade, Lib_ShashinDataObject $clonableDataObject) {
        $this->dbFacade = $dbFacade;
        $this->clonableDataObject = $clonableDataObject;
        $this->tableName = $this->clonableDataObject->getTableName();
    }

    public function setProperties(array $shortcode) {
        if ($this->useThumbnailId == true) {
            $this->setIdString($shortcode['thumbnail']);
        }

        else {
            $this->setIdString($shortcode['id']);
        }

        $this->setThumbnailSize($shortcode['size']);
        $this->setLimitClause($shortcode['limit']);
        $this->setOrderBy($shortcode['order']);
        $this->setSort($shortcode['reverse']);
        $this->setOrderByClause();
        $this->setWhereClause($shortcode['type']);
        $this->setDefaultLimitIfNeeded();
    }

    public function setUseThumbnailId($useThumbnailId) {
        if (is_bool($useThumbnailId)) {
            $this->useThumbnailId = $useThumbnailId;
        }

        return $this->useThumbnailId;
    }

    public function setIdString($idString = null) {
        $this->isAStringOfNumbersOrNull($idString);
        $this->idString = $idString;
        return $this->idString;
    }

    // this does not belong in this class
    public function setThumbnailSize($thumbnailSize = 'small') {
        if (ToppaFunctions::isPositiveNumber($thumbnailSize)) {
            $this->thumbnailSize = $thumbnailSize;
        }

        else {
            $this->isInListOfValidValues('size', $thumbnailSize);
            $this->thumbnailSize = $thumbnailSize;
        }

        return $this->thumbnailSize;
    }


    public function setLimitClause($limit = null) {
        if ($limit && !ToppaFunctions::isPositiveNumber($limit)) {
            throw new Exception(__('That is not a valid limit'));
        }

        elseif ($limit) {
            $this->limitClause = "limit $limit";
        }

        return $this->limitClause;
    }

    abstract public function setOrderBy($orderBy = null);

    public function setSort($reverse) {
        $this->isInListOfValidValues('reverse', $reverse);

        if ($reverse == 'y') {
            $this->sort ='desc';
        }

        else {
            $this->sort = 'asc';
        }

        return $this->sort;
    }

    protected function isInListOfValidValues($shortcodeKey, $value) {
        if (!in_array($value, $this->validInputValues[$shortcodeKey])) {
            throw new Exception($value . __(" is not a valid ") . $shortcodeKey . __(" value"));
        }

        return true;
    }

    protected function isAStringOfNumbersOrNull($stringOfNumbers = null) {
        // we want comma separated numbers or a null value
        if (preg_match("/^[\s\d,]+$/", $stringOfNumbers) || !$stringOfNumbers) {
        }

        else {
            throw new Exception($stringOfNumbers . " " . __("is not a valid string of numbers"));
        }

        return true;
    }

    public function getCollectionForShortcode(array $shortcode) {
        $this->setProperties($shortcode);

        if ($this->orderBy == 'user') {
            return $this->getCollectionInUserOrder();
        }

        return $this->getCollection();
    }

    public function getCollection() {
        $rows = $this->getData();

        if (!is_array($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            $dataObject = clone $this->clonableDataObject;
            $dataObject->set($row);
            $this->collection[$dataObject->id] = $dataObject;
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
                    $this->collection[$dataObject->id] = $dataObject;
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
            $this->tableName,
            null,
            null,
            $sqlConditions
        );
        return $rows;
    }

    public function setOrderByClause() {
        if ($this->orderBy != 'user') {
            $this->orderByClause = "order by " . $this->orderBy . " " . $this->sort;
        }

        return $this->orderByClause;
    }

    public function setWhereClause($type = null) {
        if ($type == 'albumphotos') {
            $this->whereClause = "where albumId in (" . $this->idString . ")";
        }

        elseif ($this->idString) {
            $this->whereClause = "where id in (" . $this->idString . ")";
        }

        return $this->whereClause;
    }

    public function setLimitNeeded($limitNeeded) {
        if (is_bool($limitNeeded)) {
            $this->limitNeeded = $limitNeeded;
        }

        return $this->limitNeeded;
    }

    public function setDefaultLimitIfNeeded() {
        if (!$this->idString && !$this->limitClause && $this->limitNeeded == true) {
            $this->setLimitClause($this->defaultLimit);
        }

        return $this->limitClause;
    }
}
