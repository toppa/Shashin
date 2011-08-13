<?php

abstract class Public_ShashinPhotoDisplayerPicasa extends Public_ShashinDataObjectDisplayer {
    public function __construct() {
        $this->validSizes = array(32, 48, 64, 72, 104, 144, 150, 160, 94, 110, 128, 200, 220, 288, 320, 400, 512, 576, 640, 720, 800, 912, 1024, 1152, 1280, 1440, 1600);
        $this->validCropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        $this->sizesMap = array(
            'xsmall' => 72,
            'small' => 160,
            'medium' => 320,
            'large' => 640,
            'xlarge' => 800,
        );
        parent::__construct();
    }

    public function setImgAltAndTitle() {
        // there may already be entities in the description, so we want to be very
        // conservative with what we replace
        $this->imgAltAndTitle = str_replace('"', '&quot;', $this->dataObject->description);
        return $this->imgAltAndTitle;
    }

    public function setImgSrc() {
        $this->imgSrc = $this->thumbnail->contentUrl;
        $this->imgSrc .= '?imgmax=' . $this->actualSize;

        if ($this->displayCropped) {
            $this->imgSrc .= '&amp;crop=1';
        }

        return $this->imgSrc;
    }

    public function setCaption() {
        if ($this->shortcode->caption == 'y' && $this->dataObject->description) {
            $this->caption = '<span class="shashin3alpha_thumb_caption">'
                . $this->dataObject->description
                . '</span>';
        }

        return $this->caption;
    }
}
