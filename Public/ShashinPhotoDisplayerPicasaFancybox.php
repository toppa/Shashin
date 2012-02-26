<?php

class Public_ShashinPhotoDisplayerPicasaFancybox extends Public_ShashinPhotoDisplayerPicasa {
    public function __construct() {
        parent::__construct();
    }

    public function setLinkOnClickVideo() {
        // don't let the videos be larger than 80% of the largest desired photo size
        $maxVideoWidth = $this->actualExpandedSize * .8;

        if ($this->dataObject->videoWidth > $maxVideoWidth) {
            $heightRatio = $maxVideoWidth / $this->dataObject->videoWidth;
            $width = $maxVideoWidth;
            $height = $this->dataObject->videoHeight * $heightRatio;
        }

        else {
            $width = $this->dataObject->videoWidth;
            $height = $this->dataObject->videoHeight;
        }

        $this->linkOnClick = "return shashinFancyboxVideo('test title', $width, $height, '{$this->linkHref}')";
        return $this->linkOnClick;
    }

    public function setImgTitle() {
        $this->imgTitle = null;
        return $this->imgTitle;
    }

    public function setLinkRel() {
        $this->linkRel = 'shashinFancybox_' . $this->sessionManager->getGroupCounter();
        return $this->linkRel;
    }

    public function setLinkRelVideo() {
        $this->linkRel = 'shashinFancybox_' . $this->sessionManager->getGroupCounter();
        return $this->linkRel;
    }

    // htmlspecialchars lets us put links within the title (but we want single quotes,
    // which are not converted to entities)
    public function setLinkTitle() {
        $this->linkTitle = htmlspecialchars(str_replace('"', "'", $this->setOriginalPhotoLinkForCaption()))
            . $this->dataObject->description
            . htmlspecialchars(str_replace('"', "'", $this->setExifDataForCaption()));
        return $this->linkTitle;
    }

    public function setLinkClass() {
        $this->linkClass = 'shashinFancybox';
        return $this->linkClass;
    }
}