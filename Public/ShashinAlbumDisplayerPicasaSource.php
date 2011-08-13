<?php

class Public_ShashinAlbumDisplayerPicasaSource extends Public_ShashinAlbumDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkHref() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }
}