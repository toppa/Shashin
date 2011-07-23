<?php

class Lib_ShashinPhotoDisplayerPicasa extends Lib_ShashinPhotoDisplayer {
    public function __construct(Lib_ShashinPhoto $photo) {
        $this->validSizes = array(32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800);
        $this->validCropSizes = array(32, 48, 64, 160);
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
        $this->imgSrc = $this->photo->contentUrl;
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
