<?php

class Public_ShashinPhotoDisplayerPicasaHighslide extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkHref() {
        $this->linkHref = $this->dataObject->contentUrl
            . '?imgmax=' . $this->actualExpandedSize;
        return $this->linkHref;
    }

    public function setLinkHrefVideo() {
        $this->linkHref = 'http://video.google.com/googleplayer.swf?videoUrl='
            . urlencode(html_entity_decode($this->dataObject->videoUrl))
            . '&amp;autoPlay=true';
        return $this->linkHref;
    }

    public function setLinkOnClick() {
        $this->linkOnClick = 'return hs.expand(this, { ';
        $this->linkOnClick .= $this->appendLinkOnClick();
        return $this->linkOnClick;
    }

    public function setLinkOnClickVideo() {
        // need minWidth because width was not autosizing for content
        // need "preserveContent: false" so the video and audio will stop when the window is closed
        $width = $this->dataObject->videoWidth;
        $height = $this->dataObject->videoHeight;
        $this->linkOnClick = "return hs.htmlExpand(this, { objectType:'swf', "
                . 'minWidth: ' . ($width+20)
                . ', minHeight: ' . ($height+20)
                . ", objectWidth: $width"
                . ", objectHeight: $height"
                . ", allowSizeReduction: false, preserveContent: false, ";
        $this->linkOnClick .= $this->appendLinkOnClick();
        return $this->linkOnClick;
    }

    private function appendLinkOnClick() {
        return "autoplay: "
            . $this->settings->highslideAutoplay
            . ", slideshowGroup: 'group"
            . $this->sessionManager->getGroupCounter()
            . "' })";
    }

    public function setLinkClass() {
        $this->linkClass = 'highslide';
        return $this->linkClass;
    }

    public function setCaption() {
        parent::setCaption();
        $this->caption .= PHP_EOL . '<div class="highslide-caption">';

        if ($this->dataObject->description) {
            $this->caption .= $this->dataObject->description;
        }

        $this->caption .= $this->formatExifDataForCaption();
        $this->caption .= '</div>';
        return $this->caption;
    }
}
