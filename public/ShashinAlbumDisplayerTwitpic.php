<?php

class Public_ShashinAlbumDisplayerTwitpic extends Public_ShashinAlbumDisplayer {
    public function __construct() {
        $this->validThumbnailSizes = array(1280); // we can't know, so pick something big
        parent::__construct();
    }

    public function setImgSrc() {
        $this->imgSrc = str_replace('_normal.', '.', $this->thumbnail->coverPhotoUrl);
        $this->makeImgSrcProtocolConsistent();
        return $this->imgSrc;
    }
}
