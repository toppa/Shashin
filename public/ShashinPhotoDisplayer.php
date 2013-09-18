<?php

abstract class Public_ShashinPhotoDisplayer extends Public_ShashinDataObjectDisplayer {

    public function __construct() {
        $this->expandedSizesMap = array(
            'xsmall' => 400,
            'small' => 640,
            'medium' => 800,
            'large' => 912,
            'xlarge' => 1024,
        );
        parent::__construct();
    }

    public function setImgAlt() {
        $this->imgAlt = $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
        return $this->imgAlt;
    }

    public function setImgTitle() {
        $this->imgTitle = $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
        return $this->imgTitle;
    }

    public function setCaption() {
        if ($this->shortcode->caption == 'y' && $this->dataObject->description) {
            $this->caption = '<span class="shashinThumbnailCaption">'
                . $this->dataObject->description
                . '</span>';
        }

        return $this->caption;
    }

    public function setActualExpandedSize() {
        if (array_key_exists($this->settings->expandedImageSize, $this->expandedSizesMap)) {
            $this->actualExpandedSize = $this->expandedSizesMap[$this->settings->expandedImageSize];
        }

        else {
            throw New Exception("invalid size requested");
        }

        return $this->actualExpandedSize;
    }

    public function setLinkIdForImg() {
        $linkId = $this->setLinkIdParts();
        $this->linkIdForImg = 'shashinThumbnailLink_' . $linkId;
        return $this->linkIdForImg;
    }

    private function setLinkIdParts() {
        $linkId = $this->sessionManager->getThumbnailCounter();

        if ($this->albumIdForAjaxPhotoDisplay) {
            $linkId .= '_' . $this->albumIdForAjaxPhotoDisplay;
        }

        return $linkId;
    }
    // degenerate
    public function setLinkIdForCaption() {
        return null;
    }

    //  putting this here to avoid repeating it in every fancybox subclass - need PHP 5.3 traits!
    public function setCaptionForFancybox() {
        $linkId = $this->setLinkIdParts();
        $this->caption .= '<div class="shashinFancyboxCaptionWrapper" id="shashinFancyboxCaption_'
            . $linkId
            . '">' . PHP_EOL;
        $this->caption .= '<div class="shashinFancyboxCaption">' . PHP_EOL;
        $this->caption .= '<div class="shashinFancyboxCaptionClose">'
            . '<a href="#" onclick="jQuery.fancybox.close();">'
            . '<img alt="Close" src="'
            . $this->functionsFacade->getPluginsUrl('/display/', __FILE__)
            . 'fancybox/closelabel.gif" />'
            . '</a>'
            . '</div>' . PHP_EOL;

        $exifCaption = $this->setExifDataForCaption();

        if ($this->dataObject->description || $exifCaption) {
            $this->caption .= '<strong>';

            if ($this->dataObject->description) {
                $this->caption .= $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
            }

            if ($exifCaption) {
                $this->caption .= $exifCaption;
            }

            $this->caption .= '</strong>';
        }

        // leave this comment - shashin.js looks for it and will manipulate this closing div
        $this->caption .= $this->setDivOriginalPhotoLinkForCaption();
        $this->caption .= '<!-- comment for image counter --></div>' . PHP_EOL;
        $this->caption .= '</div>' . PHP_EOL;
        return $this->caption;
    }

    //  putting this here to avoid repeating it in every Prettyphoto subclass - need PHP 5.3 traits!
    public function setCaptionForPrettyphoto() {
        $linkId = $this->setLinkIdParts();
        $this->caption .= '<div class="shashinPrettyphotoCaptionWrapper" id="shashinPrettyphotoCaption_'
            . $linkId
            . '">' . PHP_EOL;
        $this->caption .= '<div class="shashinPrettyPhotoCaption">' . PHP_EOL;
        $exifCaption = $this->setExifDataForCaption();

        if ($this->dataObject->description || $exifCaption) {
            if ($this->dataObject->description) {
                $this->caption .= $this->functionsFacade->htmlSpecialCharsOnce($this->dataObject->description);
            }

            if ($exifCaption) {
                $this->caption .= $exifCaption;
            }
        }

        $this->caption .= $this->setDivOriginalPhotoLinkForCaption();
        $this->caption .= '</div></div>' . PHP_EOL;
        return $this->caption;
    }

    // twitpic community guidelines require a link back to the original photo,
    // and it's nice to acknowledge the others too
    public function setOriginalPhotoLinkForCaption() {
        return '<a href="' . $this->dataObject->linkUrl . '">'
            . __('View at', 'shashin')
            . ' ' . ucfirst($this->dataObject->albumType)
            . '</a>';
    }

    public function setDivOriginalPhotoLinkForCaption() {
        return '<div class="shashinLinkToOriginalPhoto">'
            . $this->setOriginalPhotoLinkForCaption()
            . '</div>'
            . PHP_EOL;
    }

    public function setExifDataForCaption() {
        $exifCaption = null;
        $exifParts = array();
        $photoData = $this->dataObject->getData();

        switch ($this->settings->captionExif) {
            case'date':
                if ($photoData['takenTimestamp'])
                    $exifParts[] = $this->setDateForCaption($photoData['takenTimestamp']);
                break;
            case 'none':
                break;
            case 'all':
            default:
                if ($photoData['takenTimestamp'])
                    $exifParts[] = $this->setDateForCaption($photoData['takenTimestamp']);
                if ($photoData['make'])
                    $exifParts[] = $photoData['make'] . " " . $photoData['model'];
                if ($photoData['fstop'])
                    $exifParts[] =  $photoData['fstop'];
                if ($photoData['focalLength'])
                    $exifParts[] = $photoData['focalLength'] . "mm";
                if ($photoData['exposure'])
                    $exifParts[] = $photoData['exposure'] . " sec";
                if ($photoData['iso'])
                    $exifParts[] = "ISO " . $photoData['iso'];
        }

        if (!empty($exifParts)) {
            $exifCaption .= '<span class="shashinCaptionExif">'
                . implode(', ', $exifParts)
                . '</span>';
        }

        return $exifCaption;
    }

    public function setDateForCaption($date = null) {
        if (!$date) {
            return null;
        }

        return $this->functionsFacade->dateI18n("d-M-Y H:i", $date);
    }

    public function adjustVideoDimensions() {
        $dimensions = array();
        // don't let the videos be larger than 80% of the largest desired photo size
        // (best guess based on typical video quality)
        $maxVideoWidth = $this->actualExpandedSize * .8;

        if ($this->dataObject->videoWidth > $maxVideoWidth) {
            $heightRatio = $maxVideoWidth / $this->dataObject->videoWidth;
            $dimensions['width'] = $maxVideoWidth;
            $dimensions['height'] = $this->dataObject->videoHeight * $heightRatio;
        }

        else {
            $dimensions['width'] = $this->dataObject->videoWidth;
            $dimensions['height'] = $this->dataObject->videoHeight;
        }

        return $dimensions;
    }
}
