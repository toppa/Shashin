<?php

abstract class Public_ShashinPhotoDisplayerYoutube extends Public_ShashinPhotoDisplayer {
    public function __construct() {
        $this->validThumbnailSizes = array(120, 480);
        $this->validCropSizes = array();
        parent::__construct();
    }

    public function setImgSrc() {
        if ($this->actualThumbnailSize == 480) {
            $this->imgSrc = $this->thumbnail->contentUrl;
        }

        else {
            $this->imgSrc = str_replace('/0.jpg', '/2.jpg', $this->thumbnail->contentUrl);
        }

        $this->makeImgSrcProtocolConsistent();
        return $this->imgSrc;
    }

    // degenerate
    public function setLinkHref() {
        return null;
    }

    public function setLinkHrefVideo() {
        // extract the ID of the video
        $urlParts = explode('?', $this->dataObject->videoUrl);
        $pathParts = explode('/', $urlParts[0]);
        $this->linkHref = 'http://www.youtube.com/embed/'
            . $pathParts[(count($pathParts) - 1)]
            . '?rel=0&amp;fs=0&amp;wmode=transparent';
        return $this->linkHref;
    }
}
