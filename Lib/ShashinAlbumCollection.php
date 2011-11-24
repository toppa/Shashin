<?php

class Lib_ShashinAlbumCollection extends Lib_ShashinDataObjectCollection {
    public function __construct() {
    }

    public function setOrderBy() {
        switch ($this->shortcode->order) {
            case 'id':
                $this->orderBy = 'id';
                break;
            case 'date':
                $this->orderBy = 'pubDate';
                break;
            case 'uploaded':
                throw New Exception(__('"uploaded" is not allowed for ordering albums', 'shashin'));
                break;
            case 'filename':
                throw New Exception(__('"filename" is not allowed for ordering albums', 'shashin'));
                break;
            case 'title':
                $this->orderBy = 'title';
                break;
            case 'location':
                $this->orderBy = 'location';
                break;
            case 'count':
                $this->orderBy = 'photoCount';
                break;
            case 'sync':
                $this->orderBy = 'lastSync';
                break;
            case 'random':
                $this->orderBy = "rand()";
                break;
            case 'source':
                throw New Exception(__('"source" is not allowed for ordering albums', 'shashin'));
                break;
            case 'user':
                $this->orderBy = 'user';
                break;
            default:
                if ($this->idString) {
                    $this->orderBy = 'user';
                }

                else {
                    $this->orderBy = 'pubDate';
                }
        }

        return $this->orderBy;
    }
}
