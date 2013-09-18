<?php

class Public_ShashinAlbumDisplayerYoutube extends Public_ShashinAlbumDisplayer {
    public function __construct() {
        $this->validThumbnailSizes = array(123);
        parent::__construct();
    }

    public function setImgSrc() {
        $this->imgSrc = $this->thumbnail->coverPhotoUrl;
        $this->makeImgSrcProtocolConsistent();
        return $this->imgSrc;
    }
}
