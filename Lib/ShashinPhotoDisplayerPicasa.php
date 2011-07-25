<?php

class Lib_ShashinPhotoDisplayerPicasa extends Lib_ShashinPhotoDisplayer {
    public function __construct(Lib_ShashinPhoto $photo) {
        $this->validSizes = array(32, 48, 64, 72, 104, 144, 150, 160, 94, 110, 128, 200, 220, 288, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440, 1600);
        $this->validCropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        $this->sizesMap = array(
            'xsmall' => 72,
            'small' => 160,
            'medium' => 320,
            'large' => 640,
            'xlarge' => 800,
        );
        parent::__construct($photo);
    }

    public function setImgSrc() {
        $this->imgSrc = $this->thumbnail->contentUrl;
        $this->imgSrc .= '?imgmax=' . $this->actualSize;

        if ($this->displayCropped) {
            $this->imgSrc .= '&amp;crop=1';
        }

        return true;
    }

    public function setAHref() {
        $this->aHref = $this->photo->linkUrl;
        return $this->aHref;
    }
}
