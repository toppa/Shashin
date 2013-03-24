<?php

class Public_ShashinPhotoDisplayerYoutubeSource extends Public_ShashinPhotoDisplayerYoutube {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkHrefVideo() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }
}