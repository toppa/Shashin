<?php

abstract class Public_ShashinAlbumDisplayerPicasa extends Public_ShashinDataObjectDisplayer {
    public function __construct() {
        // there's no way to know the actual sizes of Picasa album thumbnails
        // so we are limited to the available square crop sizes
        $this->displayCroppedRequired = true;
        $this->validThumbnailSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        $this->validCropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);
        $this->thumbnailSizesMap = array(
            'xsmall' => 72,
            'small' => 104,
            'medium' => 144,
            'large' => 150,
            'xlarge' => 160,
        );

        parent::__construct();
    }

    public function setImgTitle() {
        $this->imgTitle = $this->makeDescriptionQuotable();
        return $this->imgTitle;
    }

    public function setImgSrc() {
        // example: http://lh4.ggpht.com/_e1IlgcNcTSg/RomcGGX3G7E/AAAAAAAAEmQ/ccUn4vvp0Yw/s160-c/2007NewportRI.jpg
        $urlParts = explode('/s160-c/', $this->thumbnail->coverPhotoUrl);
        $this->imgSrc = $urlParts[0] . '/s' . $this->actualThumbnailSize . '-c/' . $urlParts[1];
        return $this->imgSrc;
    }

    // degenerate
    public function setImgClassAdditional() {
        return null;
    }

    // degenerate
    public function setActualExpandedSizeFromRequestedSize() {
        return null;
    }

    // degenerate
    public function setLinkHrefVideo() {
        return null;
    }

    // degenerate
    public function setLinkOnClickVideo() {
        return null;
    }

    // degenerate
    public function setLinkOnClick() {
        return null;
    }

    // degenerate
    public function setLinkRel() {
        return null;
    }

    // degenerate
    public function setLinkRelVideo() {
        return null;
    }

    // degenerate
    public function setLinkTitle() {
        return null;
    }

    public function setLinkHref() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }

    public function setLinkIdForImg() {
        $this->linkIdForImg = 'shashinAlbumThumbLink_img_' . $this->dataObject->id;
        return $this->linkIdForImg;
    }

    public function setLinkIdForCaption() {
        $this->linkIdForCaption = 'shashinAlbumThumbLink_caption_' . $this->dataObject->id;
        return $this->linkIdForCaption;
    }

    public function setLinkClass() {
        $this->linkClass = 'shashinAlbumThumbLink';
        return $this->linkClass;
    }

    public function setCaption() {
        if ($this->shortcode->caption != 'n') {
            $this->caption = $this->generateCaptionTitle();
            $this->caption .= $this->generateCaptionDate();
            $this->caption .= $this->generateCaptionLocationAndPhotoCount();
            return $this->caption;
        }

        return null;
    }

    private function generateCaptionTitle() {
        $caption = '<span class="shashinAlbumCaptionTitle">';
        $caption .= $this->linkTagForCaption ? $this->linkTagForCaption : '';
        $caption .= $this->dataObject->title;
        $caption .= $this->linkTagForCaption ? '</a>' : '';
        $caption .= '</span>' . PHP_EOL;
        return $caption;
    }

    private function generateCaptionDate() {
        return '<span class="shashinAlbumCaptionDate">'
            . $this->functionsFacade->dateI18n("M j, Y", $this->dataObject->pubDate) . '</span>' . PHP_EOL;
    }

    private function generateCaptionLocationAndPhotoCount() {
        if ($this->dataObject->location) {
            $caption = '<span class="shashinAlbumCaptionLocation">';
                if ($this->dataObject->geoPos) {
                    $caption .= '<a href="http://maps.google.com/maps?q='
                        . urlencode($this->dataObject->geoPos)
                        . '"><img src="'
                        . $this->functionsFacade->getPluginsUrl('/Display/mapped_sm.gif', __FILE__)
                        . '" alt="Google Maps Location" width="15" height="12" /></a>';
                }

            $caption .= 'Photos: ' . $this->dataObject->photoCount . '</span>' . PHP_EOL;
            return $caption;
        }

        return null;
    }

}
