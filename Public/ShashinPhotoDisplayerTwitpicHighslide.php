<?php

class Public_ShashinPhotoDisplayerTwitpicHighslide extends Public_ShashinPhotoDisplayerTwitpic {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkOnClick() {
        $this->linkOnClick = 'return hs.expand(this, { ';
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
        $this->caption .= '<div class="highslide-caption">';

        // twitpic community guidelines require a link back to the original photo
        $this->caption .= ' <div style="float:right;"><a href="' . $this->dataObject->linkUrl . '">';
        $this->caption .= __('View at Twitpic', 'shashin');
        $this->caption .= '</a></div>';

        if ($this->dataObject->description) {
            $this->caption .= $this->dataObject->description;
        }

        $this->caption .= $this->formatExifDataForCaption();
        $this->caption .= '</div>';
        return $this->caption;
    }
}
