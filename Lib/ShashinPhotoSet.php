<?php

class Lib_ShashinPhotoSet {
    private $dbFacade;
    private $clonablePhoto;
    private $photoRef;
    private $clonableAlbum;
    private $albumRef;
    private $photoTableName;
    private $albumTableName;
    private $photoSet = array();
    private $photoKeys = array();

    public function __construct(&$dbFacade, &$clonablePhoto, &$photoRef, &$clonableAlbum, &$albumRef) {
        $this->dbFacade = $dbFacade;
        $this->clonablePhoto = $clonablePhoto;
        $this->photoRef = $photoRef;
        $this->albumRef = $albumRef;
        $this->clonableAlbum = $clonableAlbum;
        $this->photoTableName = $this->clonablePhoto->getTableName();
        $this->albumTableName = $this->clonableAlbum->getTableName();
    }

    public function getPhotoSet($photoKeysString) {
        $this->setPhotoKeys($photoKeysString);
        $rows = $this->getPhotoRecords();
        $photos = $this->putRowDataInObjects($rows);
        $this->setPhotoSet($photos);
        return $this->photoSet;
    }

    public function setPhotoKeys($photoKeysString) {
        if (!preg_match('^[\d, ]+$', $photoKeysString)) {
            throw New Exception(__("Invalid photo keyss", "shashin"));
        }

        $this->photoKeys = explode(",", $photoKeysString);
        array_walk($this->photoKeys, array('ToppaFunctions', 'trimCallback'));
    }

    public function getPhotoRecords() {
        $photoKeysString = implode(",", $this->photoKeys);
        $conditions = "where photoKey in ($photoKeysString)"
            . " inner join " . $this->albumTableName . " on "
            . $this->albumTableName . ".albumId = "
            . $this->photoTableName . ".albumId";

        $rows = $this->dbFacade->sqlSelectMultipleRows($this->photoTableName, null, null, $conditions);

        if (empty($rows)) {
            throw New Exception(__("Failed to find database record for photos", "shashin"));
        }

        return $rows;
    }

    public function putRowDataInObjects($rows) {
        $photoRefData = $this->photoRef->getRefData();
        $albumRefData = $this->albumRef->getRefData();

        foreach ($rows as $k=>$v) {
            $photo = clone $this->clonablePhoto;
            $album = clone $this->clonableAlbum;

            if ($photoRefData[$k]) {
                $photo->$k = $v;
            }

            if ($albumRefData[$k]) {
                $album->$k = $v;
            }

            $photo->setAlbum($album);
            $photos[$photo->photoKey] = $photo;
        }

        return $photos;
    }

    public function setPhotoSet($photos) {
        foreach ($this->photoKeys as $key) {
            $this->photoSet[] = $photos[$key];
        }

        return true;
    }
}
