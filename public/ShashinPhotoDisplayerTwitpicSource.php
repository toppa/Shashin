<?php

class Public_ShashinPhotoDisplayerTwitpicSource extends Public_ShashinPhotoDisplayerTwitpic {
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