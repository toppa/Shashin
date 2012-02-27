<?php

abstract class Public_ShashinAlbumDisplayer extends Public_ShashinDataObjectDisplayer {
    public function __construct() {
        parent::__construct();
    }

    public function setImgAlt() {
        $this->imgAlt = $this->makeTextQuotable($this->dataObject->title);
        return $this->imgAlt;
    }

    public function setImgTitle() {
        $this->imgTitle = $this->makeTextQuotable($this->dataObject->title);
        return $this->imgTitle;
    }

    // degenerate
    public function setActualExpandedSize() {
        return null;
    }

    public function setLinkHref() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }

    // degenerate
    public function setLinkHrefVideo() {
        return null;
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
            $this->generateCaptionTitle();
            $this->generateCaptionDate();
            $this->generateCaptionLocationAndPhotoCount();
        }

        return $this->caption;
    }

    private function generateCaptionTitle() {
        $this->caption = '<span class="shashinAlbumCaptionTitle">';
        $this->caption .= $this->linkTagForCaption ? $this->linkTagForCaption : '';
        $this->caption .= $this->dataObject->title;
        $this->caption .= $this->linkTagForCaption ? '</a>' : '';
        $this->caption .= '</span>';
        return $this->caption;
    }

    private function generateCaptionDate() {
        $this->caption .= '<span class="shashinAlbumCaptionDate">'
            . $this->functionsFacade->dateI18n("M j, Y", $this->dataObject->pubDate)
            . '</span>';
        return $this->caption;
    }

    private function generateCaptionLocationAndPhotoCount() {
        $this->caption .= '<span class="shashinAlbumCaptionLocation">';

        if ($this->dataObject->geoPos) {
            $this->caption .= '<a href="http://maps.google.com/maps?q='
                . urlencode($this->dataObject->geoPos)
                . '"><img src="'
                . $this->functionsFacade->getPluginsUrl('/Display/mapped_sm.gif', __FILE__)
                . '" alt="Google Maps Location" width="15" height="12" /></a> ';
        }

        $this->caption .= __('Photos', 'shashin') . ': ' . $this->dataObject->photoCount . '</span>';
        return $this->caption;
    }

    // degenerate
    public function formatExifDataForHighslideCaption() {
        return null;
    }

    // degenerate
    public function formatDateForHighslideCaption($date = null) {
        return null;
    }
}
