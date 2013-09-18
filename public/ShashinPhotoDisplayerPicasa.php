<?php

abstract class Public_ShashinPhotoDisplayerPicasa extends Public_ShashinPhotoDisplayer {
    public function __construct() {
        $this->validThumbnailSizes = array(32, 48, 64, 72, 94, 104, 110, 128, 144, 150, 160, 200, 220, 288, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440, 1600);
        $this->validCropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        parent::__construct();
    }

    public function setImgSrc() {
        $this->imgSrc = $this->thumbnail->contentUrl;
        $this->imgSrc .= '?imgmax=' . $this->actualThumbnailSize;

        if ($this->displayCropped) {
            $this->imgSrc .= '&amp;crop=1';
        }

        $this->makeImgSrcProtocolConsistent();
        return $this->imgSrc;
    }

    public function setLinkHref() {
        $this->linkHref = $this->dataObject->contentUrl
            . '?imgmax=' . $this->actualExpandedSize;
        return $this->linkHref;
    }

    public function setLinkHrefVideo() {
        $this->linkHref = 'http://video.google.com/googleplayer.swf?videoUrl='
            . urlencode(html_entity_decode($this->dataObject->videoUrl))
            . '&amp;autoPlay=true';
        return $this->linkHref;
    }
}
