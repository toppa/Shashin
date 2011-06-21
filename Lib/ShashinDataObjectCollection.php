<?php

abstract class Lib_ShashinDataObjectCollection {
    protected $dbFacade;
    protected $tagType;
    protected $keysString;
    protected $thumbnailSize;
    protected $howManyPhotos;
    protected $orderBy;
    protected $thumbnailsKeysString;
    protected $orderByClause;
    protected $whereClause;
    protected $collection = array();

    public function __construct($dbFacade) {
        $this->dbFacade = $dbFacade;
    }

    public function setTagType($tagType) {
        $this->tagType = $tagType;
    }

    public function setKeysString($keysString) {
        $this->keysString = $keysString;
    }

    public function setThumbnailSize($thumbnailSize) {
        $this->thumbnailSize = $thumbnailSize;
    }

    public function setHowManyPhotos($howManyPhotos) {
        $this->howManyPhotos = $howManyPhotos;
    }

    public function setOrderBy($orderBy) {
        $this->orderBy = $orderBy;
    }

    public function setThumbnailsKeysString($thumbnailsKeysString) {
        $this->thumbnailsKeysString = $thumbnailsKeysString;
    }

    public function setOrderByClause($orderByClause = null) {
        if (is_string($orderByClause)) {
            $this->orderByClause = $orderByClause;
        }

        elseif ($this->orderBy && $this->orderBy != 'natural') {
            $this->orderByClause = "order by " . $this->orderBy;
        }
    }

    public function setWhereClause($where = null) {
        if (is_string($where)) {
            $this->whereClause = $where;
        }
    }

    abstract public function getCollection();
    abstract public function getData();
    abstract public function getHtmlForShortcode();

}
