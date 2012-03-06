<?php

class Public_ShashinPhotoDisplayerPicasaFancybox extends Public_ShashinPhotoDisplayerPicasa {
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

    // htmlspecialchars lets us put links within the title (but we want single quotes,
    // which are not converted to entities)
    public function setLinkTitle() {
        $this->linkTitle = htmlspecialchars(str_replace('"', "'", $this->setDivOriginalPhotoLinkForCaption()))
            . $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description)
            . htmlspecialchars(str_replace('"', "'", $this->setExifDataForCaption()));
        return $this->linkTitle;
    }

    public function setLinkTitleVideo() {
        $this->linkTitle = $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description)
            . ' - '
            . htmlspecialchars(str_replace('"', "'", $this->setOriginalPhotoLinkForCaption()));
        return $this->linkTitle;
    }

    public function setLinkClass() {
        $this->linkClass = 'shashinFancybox';
        return $this->linkClass;
    }

    public function setLinkClassVideo() {
        $this->linkClass = 'shashinFancyboxVideo';
        return $this->linkClass;
    }
}