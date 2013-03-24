<?php

class Public_ShashinPhotoDisplayerPicasaSource extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkHref() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }

    public function setLinkHrefVideo() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }
}