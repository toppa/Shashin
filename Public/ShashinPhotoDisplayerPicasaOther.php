<?php

class Public_ShashinPhotoDisplayerPicasaOther extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setImgTitle() {
        $this->imgTitle = null;

        if (in_array('images', $this->settings->otherTitle)) {
            $this->imgTitle = $this->makeTextQuotable($this->dataObject->description);
        }

        return $this->imgTitle;
    }

    public function setImgClass() {
        parent::setImgClass();

        if ($this->settings->otherImageClass) {
            $this->imgClass .= ' ' . $this->settings->otherImageClass;
        }

        return $this->imgClass;
    }

    public function setLinkRel() {
        $this->linkRel = $this->settings->otherRelImage;
        $this->generateLinkRelGroupMarker();
        return $this->linkRel;
    }

    public function setLinkRelVideo() {
        $this->linkRel = $this->settings->otherRelVideo;
        $this->generateLinkRelGroupMarker();
        return $this->linkRel;
    }

    private function generateLinkRelGroupMarker() {
        if ($this->settings->otherRelDelimiter == 'brackets') {
            $this->linkRel .= '[' . $this->sessionManager->getGroupCounter() . ']';
        }

        else {
            $this->linkRel .= '-' . $this->sessionManager->getGroupCounter();
        }
    }

    public function setLinkTitle() {
        $this->linkTitle = null;

        if (in_array('links', $this->settings->otherTitle)) {
            $this->linkTitle = str_replace('"', '&quot;', $this->dataObject->description);
        }

        return $this->linkTitle;
    }

    public function setLinkClass() {
        $this->linkClass = null;

        if ($this->settings->otherLinkClass) {
            $this->linkClass = $this->settings->otherLinkClass;
        }

        return $this->linkClass;
    }
}