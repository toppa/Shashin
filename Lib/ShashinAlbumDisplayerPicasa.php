<?php

class Lib_ShashinAlbumDisplayerPicasa extends Lib_ShashinDataObjectDisplayer {
    public function __construct(
      Lib_ShashinDataObject $dataObject,
      Lib_ShashinDataObject $alternativeThumbnail = null) {

        // there's no way to know the actual sizes of Picasa album thumbnails
        // so we are limited to the available square crop sizes
        $this->displayCroppedRequired = true;
        $this->validSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        $this->validCropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        $this->sizesMap = array(
            'xsmall' => 72,
            'small' => 104,
            'medium' => 144,
            'large' => 160,
            'xlarge' => 160,
        );

        parent::__construct($dataObject, $alternativeThumbnail);
    }

    public function setImgSrc() {
        // example: http://lh4.ggpht.com/_e1IlgcNcTSg/RomcGGX3G7E/AAAAAAAAEmQ/ccUn4vvp0Yw/s160-c/2007NewportRI.jpg
        $urlParts = explode('/s160-c/', $this->thumbnail->coverPhotoUrl);
        $this->imgSrc = $urlParts[0] . '/s' . $this->actualSize . '-c/' . $urlParts[1];
        return $this->imgSrc;
    }

    public function setAHref() {
        $this->aHref = $this->dataObject->linkUrl;
        return $this->aHref;
    }
}
