<?php

abstract class Public_ShashinDataObjectDisplayer {
    protected $settings;
    protected $shortcode;
    protected $functionsFacade;
    protected $dataObject;
    protected $thumbnail;
    protected $actualSize;
    protected $displayCroppedRequired = false;
    protected $displayCropped;
    protected $imgHeight;
    protected $imgWidth;
    protected $imgSrc;
    protected $imgAltAndTitle;
    protected $imgTag;
    protected $linkHref;
    protected $linkOnClick;
    protected $linkId;
    protected $linkClass;
    protected $linkTag;
    protected $caption;
    protected $combinedTags;
    protected $validSizes = array();
    protected $validCropSizes = array();
    protected $sizesMap = array();

    /*
        'flickr' => array(
            'xsmall' => 75,
            'small' => 100,
            'medium' => 240,
            'large' => 500,
            'xlarge' => 1024,
        ),
        'twitpic' => array(
            'xsmall' => 75,
            'small' => 150,
            'medium' => 150,
            'large' => 600,
            'xlarge' => 600,
        )

 */

    public function __construct() {
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setShortcode(Public_ShashinShortcode $shortcode) {
        $this->shortcode = $shortcode;
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function setDataObject(Lib_ShashinDataObject $dataObject) {
        $this->dataObject = $dataObject;
    }

    public function setThumbnail(Lib_ShashinDataObject $thumbnail = null) {
        $this->thumbnail = $thumbnail ? $thumbnail : $this->dataObject;
    }

    public function run() {
        try {
            $this->initializeSessionIdCounter();
            $requestedSize = $this->shortcode->size ? $this->shortcode->size : 'xsmall';
            $numericSize = $this->setNumericSizeFromRequestedSize($requestedSize);
            $this->setActualSizeFromValidSizes($numericSize);
            $this->setDisplayCropped();
            $this->setImgWidthAndHeight();
            $this->setImgSrc();
            $this->setImgAltAndTitle();
            $this->setImgTag();

            if ($this->dataObject->isVideo()) {
                $this->setLinkHrefVideo();
                $this->setLinkOnClickVideo();
            }

            else {
                $this->setLinkHref();
                $this->setLinkOnClick();
            }

            $this->setLinkId();
            $this->setLinkClass();
            $this->setLinkTag();
            $this->setCaption();
            $this->setCombinedTags();
            $this->incrementSessionIdCounter();
        }

        catch (Exception $e) {
            return "<strong>" . $e->getMessage() . "</strong>";
        }

        return $this->combinedTags;
    }

    public function initializeSessionIdCounter() {
        if (!$_SESSION['shashin_id_counter']) {
            $_SESSION['shashin_id_counter'] = 1;
        }
    }

    public function setNumericSizeFromRequestedSize($requestedSize = 'xsmall') {
        if (array_key_exists($requestedSize, $this->sizesMap)) {
            $numericSize = $this->sizesMap[$requestedSize];
        }

        else {
            $numericSize = $requestedSize;
        }

        if (!is_numeric($numericSize)) {
            throw New Exception("invalid size requested");
        }

        return $numericSize;
    }

    public function setActualSizeFromValidSizes($numericSize) {
        foreach ($this->validSizes as $size) {
            if ($numericSize <= $size) {
                $this->actualSize = $size;
                break;
            }
        }

        return $this->actualSize;
    }

    public function getActualSize() {
        return $this->actualSize;
    }

    public function setDisplayCropped() {
        if ($this->shortcode->crop == 'y' || $this->displayCroppedRequired) {
            if (in_array($this->actualSize, $this->validCropSizes)) {
                $this->displayCropped = true;
            }
        }

        return $this->displayCropped;
    }

    public function setImgWidthAndHeight() {
        if ($this->displayCropped) {
            $this->imgWidth = $this->actualSize;
            $this->imgHeight = $this->actualSize;
        }

        // see if actualSize should be applied to the height or the width
        elseif ($this->thumbnail->width > $this->thumbnail->height) {
            $this->imgWidth = $this->actualSize;
            $percentage = $this->actualSize / $this->thumbnail->width;
            $this->imgHeight = $percentage * $this->thumbnail->height;
            settype($this->imgHeight, "int"); // drop any decimals
        }

        else {
            $this->imgHeight = $this->actualSize;
            $percentage = $this->actualSize / $this->thumbnail->height;
            $this->imgWidth = $percentage * $this->thumbnail->width;
            settype($this->imgWidth, "int"); // drop any decimals
        }

        return array($this->imgWidth, $this->imgHeight);
    }

    abstract public function setImgSrc();
    abstract public function setImgAltAndTitle();

    public function setImgTag() {
        $this->imgTag =
            '<img src="' . $this->imgSrc
            . '" alt="' . $this->imgAltAndTitle
            . '" title="' . $this->imgAltAndTitle
            . '" width="' . $this->imgWidth
            . '" height="' . $this->imgHeight
            . '" class="shashin3alpha_thumb_image"'
            . ' id="shashin_thumb_image_' . $_SESSION['shashin_id_counter'] . '" />';
    }

    abstract public function setLinkHref();
    abstract public function setLinkHrefVideo();
    abstract public function setLinkOnClick();
    abstract public function setLinkOnClickVideo();
    abstract public function setLinkClass();

    public function setLinkId() {
        $this->linkId = 'shashin_thumb_link_' . $_SESSION['shashin_id_counter'];
        return $this->linkId;
    }

    public function setLinkTag() {
        $this->linkTag =
            '<a href="' . $this->linkHref
            . '" id="' . $this->linkId . '"'
            . ($this->linkOnClick ? (' onclick="' . $this->linkOnClick . '"') : '')
            . ($this->linkClass ? (' class="' . $this->linkClass . '"') : '')
            . '>';
        return $this->linkTag;
    }

    abstract public function setCaption();

    public function setCombinedTags() {
        $this->combinedTags = $this->linkTag . $this->imgTag;

        if ($this->linkTag) {
            $this->combinedTags .= '</a>';
        }

        if ($this->caption) {
            $this->combinedTags .= PHP_EOL . $this->caption . PHP_EOL;
        }

        return $this->combinedTags;
    }

    public function incrementSessionIdCounter() {
        $_SESSION['shashin_id_counter']++;
    }

    public function getImgWidth() {
        return $this->imgWidth;
    }

    public function formatExifDataForCaption() {
        $exifCaption = null;
        $exifParts = array();
        $photoData = $this->dataObject->getData();

        switch ($this->settings->captionExif) {
            case'date':
                if ($this->dataObject['takenTimestamp'])
                    $exifParts[] = $this->formatDateForCaption($photoData['takenTimestamp']);
                break;
            case 'all':
            case 'none':
            case 'n':
            default:

                if ($photoData['takenTimestamp'])
                    $exifParts[] = $this->formatDateForCaption($photoData['takenTimestamp']);
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
                break;
//            case 'none':
//            default:
                // do nothing
        }

        if (!empty($exifParts)) {
            $exifCaption = '<span class="shashin_caption_exif">';
            $exifCaption .= implode(', ', $exifParts);
            $exifCaption .= '</span>';
        }

        return $exifCaption;
    }

    public function formatDateForCaption($date = null) {
        if (!$date) {
            return null;
        }

        return $this->functionsFacade->dateI18n("d-M-Y H:i", $date);
    }
}
