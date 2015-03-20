<?php

class Public_ShashinPhotoDisplayerYoutubePrettyphoto extends Public_ShashinPhotoDisplayerYoutube {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkHrefVideo() {
        $this->linkHref = $this->dataObject->linkUrl;
        return $this->linkHref;
    }

    public function setImgTitle() {
        $this->imgTitle = null;
        return $this->imgTitle;
    }

    public function setLinkRelVideo() {
        $groupNumber = $this->sessionManager->getGroupCounter();

        if ($this->albumIdForAjaxPhotoDisplay) {
            $groupNumber .= '_' . $this->albumIdForAjaxPhotoDisplay;
        }

        $this->linkRel = "shashinPrettyPhoto[$groupNumber]";
        return $this->linkRel;
    }

    public function setLinkTitleVideo() {
        $this->linkTitle = $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
        return $this->linkTitle;
    }

    public function setLinkClassVideo() {
        $this->linkClass = 'shashinPrettyPhotoVideo';
        return $this->linkClass;
    }

    public function setCaption() {
        parent::setCaption();
        return $this->setCaptionForPrettyphoto();
    }
}
