<?php

class Public_ShashinPhotoDisplayerPicasaPrettyphoto extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkRel() {
        $groupNumber = $this->sessionManager->getGroupCounter();

        if ($this->albumIdForAjaxPhotoDisplay) {
            $groupNumber .= '_' . $this->albumIdForAjaxPhotoDisplay;
        }

        $this->linkRel .= "shashinPrettyPhoto[$groupNumber]";
        return $this->linkRel;
    }

    public function setLinkRelVideo() {
        return $this->setLinkRel();
    }

    public function setLinkTitle() {
        $this->linkTitle = $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
        return $this->linkTitle;
    }

    public function setLinkTitleVideo() {
        return $this->setLinkTitle();
    }

    public function setLinkHrefVideo() {
        $dimensions = $this->adjustVideoDimensions();
        $this->linkHref = 'http://video.google.com/googleplayer.swf?'
            . "width={$dimensions['width']}"
            . "&height={$dimensions['height']}"
            . '&flashvars=videoUrl=' // prettyphoto will strip out "flashvars="
            . urlencode(html_entity_decode($this->dataObject->videoUrl))
            . '&amp;autoPlay=true';
        return $this->linkHref;
    }

    public function setCaption() {
        parent::setCaption();
        return $this->setCaptionForPrettyphoto();
    }
}