<?php

class Public_ShashinAlbumDisplayerPicasa extends Public_ShashinAlbumDisplayer {
    public function __construct() {
        $this->validThumbnailSizes = array(32, 48, 64, 72, 94, 104, 110, 128, 144, 150, 160, 200, 220, 288, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440, 1600);
        $this->validCropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        parent::__construct();
    }

    public function setImgSrc() {
        // example: http://lh4.ggpht.com/_e1IlgcNcTSg/RomcGGX3G7E/AAAAAAAAEmQ/ccUn4vvp0Yw/s160-c/2007NewportRI.jpg
        $replace = '/s' . $this->actualThumbnailSize;

        if ($this->displayCropped) {
            $replace .= '-c';
        }

        $replace .= '/';
        $this->imgSrc = str_replace('/s160-c/', $replace, $this->thumbnail->coverPhotoUrl);
        return $this->imgSrc;
    }
}
