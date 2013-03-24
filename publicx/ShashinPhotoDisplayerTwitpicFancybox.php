<?php

class Public_ShashinPhotoDisplayerTwitpicFancybox extends Public_ShashinPhotoDisplayerTwitpic {
    public function __construct() {
        parent::__construct();
    }

    public function setImgTitle() {
        $this->imgTitle = null;
        return $this->imgTitle;
    }

    public function setLinkRel() {
        $groupNumber = $this->sessionManager->getGroupCounter();

        if ($this->albumIdForAjaxPhotoDisplay) {
            $groupNumber .= '_' . $this->albumIdForAjaxPhotoDisplay;
        }

        $this->linkRel = 'shashinFancybox_' . $groupNumber;
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

    public function setLinkClass() {
        $this->linkClass = 'shashinFancybox';
        return $this->linkClass;
    }

    public function setLinkClassVideo() {
        $this->linkClass = 'shashinFancyboxVideo';
        return $this->linkClass;
    }

    public function setCaption() {
        parent::setCaption();
        return $this->setCaptionForFancybox();
    }

    // Fancybox cannot resolve url redirects, so we have to find the final url ourselves
    public function setLinkHref() {
        parent::setLinkHref();

        $this->linkHref = Lib_ShashinFunctions::followRedirect($this->linkHref);
    }

    public function setLinkHrefVideo() {
        parent::setLinkHrefVideo();

        $this->linkHref = Lib_ShashinFunctions::followRedirect($this->linkHref);
    }
}