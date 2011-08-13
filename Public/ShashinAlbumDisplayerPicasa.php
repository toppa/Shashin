<?php

abstract class Public_ShashinAlbumDisplayerPicasa extends Public_ShashinDataObjectDisplayer {
    public function __construct() {
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

        parent::__construct();
    }

    public function setImgAltAndTitle() {
        // there may already be entities in the description, so we want to be
        // conservative with what we replace
        $this->imgAltAndTitle = str_replace('"', '&quot;', $this->dataObject->title);
        $this->imgAltAndTitle = __('Photo Album', 'shashin') . ': '  . $this->imgAltAndTitle;
        return $this->imgAltAndTitle;
    }

    public function setImgSrc() {
        // example: http://lh4.ggpht.com/_e1IlgcNcTSg/RomcGGX3G7E/AAAAAAAAEmQ/ccUn4vvp0Yw/s160-c/2007NewportRI.jpg
        $urlParts = explode('/s160-c/', $this->thumbnail->coverPhotoUrl);
        $this->imgSrc = $urlParts[0] . '/s' . $this->actualSize . '-c/' . $urlParts[1];
        return $this->imgSrc;
    }

    // degenerate
    public function setLinkHrefVideo() {
        return null;
    }

    // degenerate
    public function setLinkOnClickVideo() {
        return null;
    }

    public function setLinkOnClick() {
        return null;
    }

    public function setLinkClass() {
        return null;
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
        $caption = '<span class="shashin3alpha_album_caption_title">';
        $caption .= $this->linkTag ? $this->linkTag : '';
        $caption .= $this->dataObject->title;
        $caption .= $this->linkTag ? '</a>' : '';
        $caption .= '</span>' . PHP_EOL;
        return $caption;
    }

    private function generateCaptionDate() {
        return '<span class="shashin3alpha_album_caption_date">'
            . $this->functionsFacade->dateI18n("M j, Y", $this->dataObject->pubDate) . '</span>' . PHP_EOL;
    }

    private function generateCaptionLocationAndPhotoCount() {
        if ($this->dataObject->location) {
            $caption = '<span class="shashin3alpha_album_caption_location">';
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
