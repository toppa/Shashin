<?php

class Public_ShashinPhotoDisplayerYoutubeHighslide extends Public_ShashinPhotoDisplayerYoutube {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkOnClickVideo() {
        $dimensions = $this->adjustVideoDimensions();

        // need "preserveContent: false" so the video and audio will stop when the window is closed
        $this->linkOnClick = "return hs.htmlExpand(this, { objectType:'iframe', "
                . 'width: ' . $dimensions['width']
                . ', height: ' . $dimensions['height']
                . ", allowSizeReduction: false, preserveContent: false, objectLoadTime: 'after', wrapperClassName: 'draggable-header no-footer', ";
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
