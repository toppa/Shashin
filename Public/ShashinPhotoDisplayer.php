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
        $this->imgAlt = $this->makeTextQuotable($this->dataObject->description);
        return $this->imgAlt;
    }

    public function setImgTitle() {
        $this->imgTitle = $this->makeTextQuotable($this->dataObject->description);
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
        $this->linkIdForImg = 'shashinThumbnailLink_' . $this->sessionManager->getThumbnailCounter();
        return $this->linkIdForImg;
    }

    // degenerate
    public function setLinkIdForCaption() {
        return null;
    }

    // awkward to put this here, but I don't want to duplicate it in each Highslide
    // child class, and making it another class seems like overkill (can't wait for
    // need traits!). In the child class, override setCaption, call it in the
    // parent, and then call this
    public function setCaptionForHighslide() {
        $highslideCaption = '<div class="highslide-caption">';
        $highslideCaption .= $this->formatOriginalPhotoLinkForHighslideCaption();

        if ($this->dataObject->description) {
            $highslideCaption .= $this->dataObject->description;
        }

        $highslideCaption .= $this->formatExifDataForHighslideCaption();
        $highslideCaption .= '</div>';
        return $highslideCaption;
    }

    // twitpic community guidelines require a link back to the original photo,
    // and it's nice to acknowledge the others too
    public function formatOriginalPhotoLinkForHighslideCaption() {
        return ' <div class="shashinHighslideLinkToOriginalPhoto">'
            . '<a href="' . $this->dataObject->linkUrl . '">'
            . __('View at', 'shashin')
            . ' ' . ucfirst($this->dataObject->albumType)
            . '</a></div>';
    }

    public function formatExifDataForHighslideCaption() {
        $exifCaption = null;
        $exifParts = array();
        $photoData = $this->dataObject->getData();

        switch ($this->settings->captionExif) {
            case'date':
                if ($photoData['takenTimestamp'])
                    $exifParts[] = $this->formatDateForHighslideCaption($photoData['takenTimestamp']);
                break;
            case 'none':
                break;
            case 'all':
            default:
                if ($photoData['takenTimestamp'])
                    $exifParts[] = $this->formatDateForHighslideCaption($photoData['takenTimestamp']);
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
            $exifCaption = '<span class="shashinCaptionExif">';
            $exifCaption .= implode(', ', $exifParts);
            $exifCaption .= '</span>';
        }

        return $exifCaption;
    }

    public function formatDateForHighslideCaption($date = null) {
        if (!$date) {
            return null;
        }

        return $this->functionsFacade->dateI18n("d-M-Y H:i", $date);
    }
}
