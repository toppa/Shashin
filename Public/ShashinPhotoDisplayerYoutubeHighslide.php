<?php

class Public_ShashinPhotoDisplayerYoutubeHighslide extends Public_ShashinPhotoDisplayerYoutube {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkOnClickVideo() {
        // YouTube videos are scalable - make them 80% of expanded photo sizes
        $widthRatio = ($this->actualExpandedSize / $this->dataObject->videoWidth) * .8;
        $heightRatio = ($this->actualExpandedSize / $this->dataObject->videoWidth) * .8;
        $width = $this->dataObject->videoWidth * $widthRatio;
        $height = $this->dataObject->videoHeight * $heightRatio;

        // need "preserveContent: false" so the video and audio will stop when the window is closed
        $this->linkOnClick = "return hs.htmlExpand(this, { objectType:'iframe', "
                . 'width: ' . $width
                . ', height: ' . $height
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

    public function setCaption() {
        parent::setCaption();
        $this->caption .= $this->setCaptionForHighslide();
        return $this->caption;
    }
}
