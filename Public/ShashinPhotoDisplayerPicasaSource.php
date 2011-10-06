<?php

class Public_ShashinPhotoDisplayerPicasaSource extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setImgTitle() {
        $this->imgTitle = $this->makeTextQuotable($this->dataObject->description);
        return $this->imgTitle;
    }

    public function setImgClassAdditional() {
        $this->imgClassAdditional = null;
        return $this->imgClassAdditional;
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

    public function setLinkRel() {
        $this->linkRel = null;
        return $this->linkRel;
    }

    public function setLinkRelVideo() {
        $this->linkRel = null;
        return $this->linkRel;
    }

    public function setLinkOnClickVideo() {
        $this->linkOnClick = null;
        return $this->linkOnClick;
    }

    public function setLinkTitle() {
        $this->linkTitle = null;
        return $this->linkTitle;
    }

    public function setLinkClass() {
        $this->linkClass = null;
        return $this->linkClass;
    }
}