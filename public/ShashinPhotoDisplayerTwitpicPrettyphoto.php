<?php

class Public_ShashinPhotoDisplayerTwitpicPrettyphoto extends Public_ShashinPhotoDisplayerTwitpic {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkRel() {
        $groupNumber = $this->sessionManager->getGroupCounter();

        if ($this->albumIdForAjaxPhotoDisplay) {
            $groupNumber .= '_' . $this->albumIdForAjaxPhotoDisplay;
        }

        $this->linkRel .= "prettyphoto[$groupNumber]";
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
}