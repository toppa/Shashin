<?php

class Lib_ShashinPhotoCollection extends Lib_ShashinDataObjectCollection {
    public function __construct() {
    }

    public function setOrderBy() {
        switch ($this->shortcode->order) {
            case 'id':
                $this->orderBy = 'id';
                break;
            case 'date':
                $this->orderBy = 'takenTimestamp';
                break;
            case 'uploaded':
                $this->orderBy = 'uploadedTimestamp';
                break;
            case 'filename':
                $this->orderBy = 'filename';
                break;
            case 'title':
                throw New Exception(__('"title" is not allowed for ordering photos', 'shashin'));
                break;
            case 'location':
                throw New Exception(__('"location" is not allowed for ordering photos', 'shashin'));
                break;
            case 'count':
                throw New Exception(__('"count" is not allowed for ordering photos', 'shashin'));
                break;
            case 'sync':
                throw New Exception(__('"sync" is not allowed for ordering photos', 'shashin'));
                break;
            case 'random':
                $this->orderBy = "rand()";
                break;
            case 'source':
                $this->orderBy = "sourceOrder";
                break;
            case 'user':
                $this->orderBy = 'user';
                break;
            default:
                if ($this->idString) {
                    $this->orderBy = 'user';
                }

                else {
                    $this->orderBy = 'albumId, sourceOrder';
                }
        }

        return $this->orderBy;
    }
}