<?php

class Public_ShashinPhotoDisplayerYoutubeFancybox extends Public_ShashinPhotoDisplayerYoutube {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkHrefVideo() {
        $this->linkHref = str_replace('watch?v=', 'v/', $this->dataObject->videoUrl);
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

        $this->linkRel = 'shashinFancybox_' . $groupNumber;
        return $this->linkRel;
    }

    public function setLinkTitleVideo() {
        $this->linkTitle = $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
        return $this->linkTitle;
    }

    public function setLinkClassVideo() {
        $this->linkClass = 'shashinFancyboxVideo';
        return $this->linkClass;
    }
}