<?php

class Public_ShashinPhotoDisplayerYoutubeOther extends Public_ShashinPhotoDisplayerYoutube {
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

    public function setLinkRelVideo() {
        $this->linkRel = $this->settings->otherRelVideo;
        $this->generateLinkRelGroupMarker();
        return $this->linkRel;
    }

    private function generateLinkRelGroupMarker() {
        $groupNumber = $this->sessionManager->getGroupCounter();

        if ($this->albumIdForAjaxPhotoDisplay) {
            $groupNumber .= '_' . $this->albumIdForAjaxPhotoDisplay;
        }

        if ($this->settings->otherRelDelimiter == 'brackets') {
            $this->linkRel .= "[$groupNumber]";
        }

        else {
            $this->linkRel .= "-$groupNumber";
        }

        return $this->linkRel;
    }

    public function setLinkTitle() {
        $this->linkTitle = null;

        if (in_array('links', $this->settings->otherTitle)) {
            $this->linkTitle = str_replace('"', '&quot;', $this->dataObject->description);
        }

        return $this->linkTitle;
    }

    public function setLinkTitleVideo() {
        return $this->setLinkTitle();
    }

    public function setLinkClass() {
        $this->linkClass = null;

        if ($this->settings->otherLinkClass) {
            $this->linkClass = $this->settings->otherLinkClass;
        }

        return $this->linkClass;
    }

    public function setLinkClassVideo() {
        return $this->setLinkClass();
    }
}