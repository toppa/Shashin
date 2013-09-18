<?php

abstract class Public_ShashinPhotoDisplayerTwitpic extends Public_ShashinPhotoDisplayer {
    public function __construct() {
        $this->validThumbnailSizes = array(75, 150, 600, 1280);
        $this->validCropSizes = array(75, 150);
        parent::__construct();
    }

    public function setImgSrc() {
        if ($this->displayCropped && $this->actualThumbnailSize == 75) {
            $this->imgSrc = $this->thumbnail->contentUrl;
        }

        elseif ($this->displayCropped && $this->actualThumbnailSize == 150) {
            $this->imgSrc = str_replace('/mini/', '/thumb/', $this->thumbnail->contentUrl);
        }

        elseif ($this->actualThumbnailSize == 1280) {
            $this->imgSrc = str_replace('/mini/', '/full/', $this->thumbnail->contentUrl);
        }

        else {
            $this->imgSrc = str_replace('/mini/', '/large/', $this->thumbnail->contentUrl);
        }

        $this->makeImgSrcProtocolConsistent();
        return $this->imgSrc;
    }

    public function setLinkHref() {
        switch ($this->actualExpandedSize) {
            case 400:
            case 640:
            case 800:
            case 912:
                $this->linkHref = str_replace('/mini/', '/large/', $this->thumbnail->contentUrl);
                break;
            case 1280:
                $this->linkHref = str_replace('/mini/', '/full/', $this->thumbnail->contentUrl);
                break;
            default:
                throw New Exception(__('Unrecognized actualExpandedSize', 'shashin'));
        }

        return $this->linkHref;
    }

    // degenerate
    public function setLinkHrefVideo() {
        return null;
    }
}
