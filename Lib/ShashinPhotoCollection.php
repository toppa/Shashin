<?php

class Lib_ShashinPhotoCollection extends Lib_ShashinDataObjectCollection {
    private $clonablePhoto;
    private $photoTableName;
    private $photoData;
    private $photoCollection = array();

    public function __construct($dbFacade, $clonablePhoto, $clonableAlbum) {
        $this->clonablePhoto = $clonablePhoto;
        $this->clonableAlbum = $clonableAlbum;
        $this->photoTableName = $this->clonablePhoto->getTableName();
        $this->albumTableName = $this->clonableAlbum->getTableName();
        parent::__construct($dbFacade);
    }

    public function getCollection() {
        $rows = $this->getData();

        foreach ($rows as $row) {
            $photo = clone $this->clonablePhoto;
            $photo->set($row);
            $this->collection[$photo->photoKey] = $photo;
        }

        return $this->collection;
    }

    public function getData() {
        $rows = $this->dbFacade->sqlSelectMultipleRows($this->photoTableName, null, $this->whereClause, $this->orderByClause);

        if (empty($rows)) {
            throw New Exception(__("Failed to find database record for photos", "shashin"));
        }

        return $rows;
    }

    public function getHtmlForShortcode() {
        $this->setOrderByClause();

        switch ($this->tagType) {
            case 'photos':
                if ($this->keysString) {
                    $this->whereClause = "where photoKey in (" . $this->keysString . ")";
                }

                else {
                    throw New Exception(__("photo keys must be provided", "shashin"));
                }

                break;
            case 'random':
                if ($this->keysString) {
                    $this->whereClause = "where albumKey in (" . $this->keysString . ")";
                }

                else {
                    throw New Exception(__("album keys must be provided", "shashin"));
                }

                break;
            case 'new':
                if ($this->keysString) {
                    $this->whereClause = "where albumKey in (" . $this->keysString . ")";
                }

                else {
                    throw New Exception(__("album keys must be provided", "shashin"));
                }

                break;
        }

        $this->getCollection();
    }
}


/*









    public function getHtmlForShortcode() {
        $this->setOrderByClause();

        switch ($this->tagType) {
            case 'photos':
                if ($this->keysString) {
                    $this->dataRows = $this->getPhotosByKeys();
                }

                else {
                    throw New Exception(__("photo keys must be provided", "shashin"));
                }

                $this->createPhotoObjects();
                break;
            case 'random':
                if ($this->keysString) {
                    $this->dataRows = $this->getRandomByKeys();
                }

                else {
                    throw New Exception(__("album keys must be provided", "shashin"));
                }

                $this->createPhotoObjects();
                break;
            case 'new':
                if ($this->keysString) {
                    $this->dataRows = $this->getNewestByKeys();
                }

                else {
                    throw New Exception(__("album keys must be provided", "shashin"));
                }

                $this->createPhotoObjects();
                break;
        }

        return true;
    }



    public function getPhotosByKeys() {
        $where = "where photoKey in (" . $this->keysString
            . ") inner join " . $this->albumTableName . " on "
            . $this->albumTableName . ".albumKey = "
            . $this->photoTableName . ".albumKey";

        $this->dataRows = $this->dbFacade->sqlSelectMultipleRows($this->photoTableName, null, $where, $this->orderByClause);

        if (empty($this->dataRows)) {
            throw New Exception(__("Failed to find database record for photos", "shashin"));
        }

        return $this->dataRows;
    }

    public function getRandomByKeys() {

    }

    public function getNewestByKeys() {

    }

    public function createPhotoObjects() {
        if ($this->orderBy == 'natural' && $this->tagType == 'photos') {
            $this->putPhotoRowsInObjectsByNaturalOrder();
        }

        else {
            $this->putPhotoRowsInObjects();
        }

        return true;
    }

    public function putPhotoRowsInObjectsByNaturalOrder() {
        $keys = explode(",", $this->keysString);
        array_walk($keys, array('ToppaFunctions', 'trimCallback'));
        $photoRefData = $this->clonablePhoto->getRefData();
        $albumRefData = $this->clonableAlbum->getRefData();

        foreach ($keys as $key) {
            foreach ($this->dataRows as $k=>$v) {
                if ($k == 'photoKey' && $key == $v) {
                    $photo = clone $this->clonablePhoto;
                    $album = clone $this->clonableAlbum;

                    if ($photoRefData[$k]) {
                        $photo->$k = $v;
                    }

                    if ($albumRefData[$k]) {
                        $album->$k = $v;
                    }

                    $photo->setAlbum($album);
                    $this->photoCollection[] = $photo;
                }
            }
        }
    }

    public function putPhotoRowsInObjects() {
        $photoRefData = $this->clonablePhoto->getRefData();
        $albumRefData = $this->clonableAlbum->getRefData();

        foreach ($this->dataRows as $k=>$v) {
            $photo = clone $this->clonablePhoto;
            $album = clone $this->clonableAlbum;

            if ($photoRefData[$k]) {
                $photo->$k = $v;
            }

            if ($albumRefData[$k]) {
                $album->$k = $v;
            }

            $photo->setAlbum($album);
            $this->photoCollection[] = $photo;
        }
    }

    public function createAlbumObjects() {
        if ($this->orderBy == 'natural' && $this->tagType == 'albums') {
            $this->putAlbumRowsInObjectsByNaturalOrder();
        }

        else {
            $this->putAlbumRowsInObjects();
        }

        return true;
    }

    public function putAlbumRowsInObjectsByNaturalOrder() {
        $keys = explode(",", $this->keysString);
        array_walk($keys, array('ToppaFunctions', 'trimCallback'));
        $albumRefData = $this->clonableAlbum->getRefData();

        foreach ($keys as $key) {
            foreach ($this->dataRows as $k=>$v) {
                if ($k == 'albumKey' && $key == $v) {
                    $album = clone $this->clonableAlbum;

                    if ($albumRefData[$k]) {
                        $album->$k = $v;
                    }

                    $this->photoCollection[] = $album;
                }
            }
        }
    }

    public function putPhotoRowsInObjects2() {
        $photoRefData = $this->clonablePhoto->getRefData();
        $albumRefData = $this->clonableAlbum->getRefData();

        foreach ($this->dataRows as $k=>$v) {
            $photo = clone $this->clonablePhoto;
            $album = clone $this->clonableAlbum;

            if ($photoRefData[$k]) {
                $photo->$k = $v;
            }

            if ($albumRefData[$k]) {
                $album->$k = $v;
            }

            $photo->setAlbum($album);
            $this->photoCollection[] = $photo;
        }
    }

}

*/