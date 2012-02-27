<?php

class Public_ShashinPhotoDisplayerTwitpicOther extends Public_ShashinPhotoDisplayerTwitpic {
    public function __construct() {
        parent::__construct();
    }

    public function setImgTitle() {
        if (in_array('images', $this->settings->otherTitle)) {
            return parent::setImgTitle();
        }

        return null;
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

    // degenerate
    public function setLinkRelVideo() {
        return null;
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