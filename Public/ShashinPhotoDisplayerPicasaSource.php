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

    public function setLinkOnClick() {
        $this->linkOnClick = null;
        return $this->linkOnClick;
    }

    public function setLinkOnClickVideo() {
        $this->linkOnClick = null;
        return $this->linkOnClick;
    }

    public function setLinkClass() {
        $this->linkClass = null;
        return $this->linkClass;
    }
}