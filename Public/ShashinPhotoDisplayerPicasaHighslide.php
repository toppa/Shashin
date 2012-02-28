<?php

class Public_ShashinPhotoDisplayerPicasaHighslide extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkOnClick() {
        $this->linkOnClick = 'return hs.expand(this, { ';
        $this->linkOnClick .= $this->appendLinkOnClick();
        return $this->linkOnClick;
    }

    public function setLinkOnClickVideo() {
        $dimensions = $this->adjustVideoDimensions();

        // need minWidth because width was not autosizing for content
        // need "preserveContent: false" so the video and audio will stop when the window is closed
        $this->linkOnClick = "return hs.htmlExpand(this, { objectType:'swf', "
                . 'minWidth: ' . ($dimensions['width'] + 20)
                . ', minHeight: ' . ($dimensions['height'] +20)
                . ", objectWidth: {$dimensions['width']}"
                . ", objectHeight: {$dimensions['height']}"
                . ", allowSizeReduction: false, preserveContent: false, ";
        $this->linkOnClick .= $this->appendLinkOnClick();
        return $this->linkOnClick;
    }

    private function appendLinkOnClick() {
        $groupNumber = $this->sessionManager->getGroupCounter();

        if ($this->albumIdForAjaxHighslideDisplay) {
            $groupNumber .= '_' . $this->albumIdForAjaxHighslideDisplay;
        }

        return "autoplay: "
            . $this->settings->highslideAutoplay
            . ", slideshowGroup: 'group"
            . $groupNumber
            . "' })";
    }

    public function setLinkClass() {
        $this->linkClass = 'highslide';
        return $this->linkClass;
    }

    public function setLinkClassVideo() {
        return $this->setLinkClass();
    }

    public function setCaption() {
        parent::setCaption();
        $this->caption .= $this->setCaptionForHighslide();
        return $this->caption;
    }
}
