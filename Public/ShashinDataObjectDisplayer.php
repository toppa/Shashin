<?php

abstract class Public_ShashinDataObjectDisplayer {
    protected $settings;
    protected $shortcode;
    protected $functionsFacade;
    protected $dataObject;
    protected $thumbnail;
    protected $sessionManager;
    protected $actualThumbnailSize;
    protected $actualExpandedSize;
    protected $displayCroppedRequired = false;
    protected $displayCropped;
    protected $imgHeight;
    protected $imgWidth;
    protected $imgSrc;
    protected $imgAlt;
    protected $imgTitle;
    protected $imgClassAdditional;
    protected $imgTag;
    protected $linkHref;
    protected $linkOnClick;
    protected $linkRel;
    protected $linkTitle;
    protected $linkIdForImg;
    protected $linkIdForCaption;
    protected $linkClass;
    protected $linkTagForImg;
    protected $linkTagForCaption;
    protected $caption;
    protected $combinedTags;
    protected $validThumbnailSizes = array();
    protected $validCropSizes = array();
    protected $validExpandedSizes = array();
    protected $thumbnailSizesMap = array();
    protected $expandedSizesMap = array();

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

    public function setSessionManager(Public_ShashinSessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
        return $this->sessionManager;
    }

    public function run() {
        try {
            $this->initializeSessionIdCounter();
            $requestedSize = $this->shortcode->size ? $this->shortcode->size : 'xsmall';
            $numericSize = $this->setNumericThumbnailSizeFromRequestedSize($requestedSize);
            $this->setActualThumbnailSizeFromValidSizes($numericSize);
            $this->setActualExpandedSizeFromRequestedSize();
            $this->setDisplayCropped();
            $this->setImgWidthAndHeight();
            $this->setImgSrc();
            $this->setImgAlt();
            $this->setImgTitle();
            $this->setImgClassAdditional();
            $this->setImgTag();

            if ($this->dataObject->isVideo()) {
                $this->setLinkHrefVideo();
                $this->setLinkOnClickVideo();
                $this->setLinkRelVideo();
            }

            else {
                $this->setLinkHref();
                $this->setLinkOnClick();
                $this->setLinkRel();
            }

            $this->setLinkTitle();
            $this->setLinkIdForImg();
            $this->setLinkIdForCaption();
            $this->setLinkClass();
            $this->setLinkTagForImg();
            $this->setLinkTagForCaption();
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
        if (!$this->sessionManager->getThumbnailCounter()) {
            $this->sessionManager->setThumbnailCounter(1);
        }
    }

    public function setNumericThumbnailSizeFromRequestedSize($requestedSize = 'xsmall') {
        if (array_key_exists($requestedSize, $this->thumbnailSizesMap)) {
            $numericSize = $this->thumbnailSizesMap[$requestedSize];
        }

        elseif ($requestedSize == 'max') {
            $numericSize = floor($this->settings->themeMaxSize / $this->shortcode->columns);
            $numericSize -= 10; // guess for padding/margins per image
        }

        else {
            $numericSize = $requestedSize;
        }

        if (!is_numeric($numericSize)) {
            throw New Exception(__('invalid size requested', 'shashin'));
        }

        return $numericSize;
    }

    public function setActualThumbnailSizeFromValidSizes($numericSize) {
        for ($i = 0; $i < count($this->validThumbnailSizes); $i++) {
            if ($numericSize == $this->validThumbnailSizes[$i]) {
                $this->actualThumbnailSize = $this->validThumbnailSizes[$i];
                break;
            }

            elseif ($numericSize < $this->validThumbnailSizes[$i]) {
                $nextSmaller = ($i == 0) ? 0 : ($i - 1);
                $this->actualThumbnailSize = $this->validThumbnailSizes[$nextSmaller];
                break;
            }
        }

        if (!$this->actualThumbnailSize) {
            $lastPosition = count($this->validThumbnailSizes) - 1;
            $this->actualThumbnailSize = $this->validThumbnailSizes[$lastPosition];
        }

        return $this->actualThumbnailSize;
    }

    abstract public function setActualExpandedSizeFromRequestedSize();

    public function getActualThumbnailSize() {
        return $this->actualThumbnailSize;
    }

    public function setDisplayCropped() {
        if ($this->shortcode->crop == 'y' || $this->displayCroppedRequired) {
            if (in_array($this->actualThumbnailSize, $this->validCropSizes)) {
                $this->displayCropped = true;
            }
        }

        return $this->displayCropped;
    }

    public function setImgWidthAndHeight() {
        if ($this->displayCropped) {
            $this->imgWidth = $this->actualThumbnailSize;
            $this->imgHeight = $this->actualThumbnailSize;
        }

        // see if actualThumbnailSize should be applied to the height or the width
        elseif ($this->thumbnail->width > $this->thumbnail->height) {
            $this->imgWidth = $this->actualThumbnailSize;
            $percentage = $this->actualThumbnailSize / $this->thumbnail->width;
            $this->imgHeight = $percentage * $this->thumbnail->height;
            settype($this->imgHeight, "int"); // drop any decimals
        }

        else {
            $this->imgHeight = $this->actualThumbnailSize;
            $percentage = $this->actualThumbnailSize / $this->thumbnail->height;
            $this->imgWidth = $percentage * $this->thumbnail->width;
            settype($this->imgWidth, "int"); // drop any decimals
        }

        return array($this->imgWidth, $this->imgHeight);
    }

    abstract public function setImgSrc();
    abstract public function setImgAlt();
    abstract public function setImgTitle();

    protected function makeTextQuotable($text) {
        // there may already be entities in the text, so we want to be very
        // conservative with what we replace
        return str_replace('"', '&quot;', $text);
    }

    abstract public function setImgClassAdditional();

    public function setImgTag() {
        $this->imgTag =
            '<img src="' . $this->imgSrc . '"'
            . ' alt="' . $this->imgAlt . '"'
            . ($this->imgTitle ? (' title="' . $this->imgTitle . '"') : '')
            . ' width="' . $this->imgWidth . '"'
            . ' height="' . $this->imgHeight . '"'
            . ' class="shashinThumbnailImage'
            . ($this->imgClassAdditional ? (' ' . $this->imgClassAdditional) : '') . '"'
            . ' id="shashinThumbnailImage_' . $this->sessionManager->getThumbnailCounter() . '" />';
        return $this->imgTag;
    }

    abstract public function setLinkHref();
    abstract public function setLinkHrefVideo();
    abstract public function setLinkOnClick();
    abstract public function setLinkRel();
    abstract public function setLinkOnClickVideo();
    abstract public function setLinkRelVideo();
    abstract public function setLinkTitle();
    abstract public function setLinkClass();
    abstract public function setLinkIdForImg();
    abstract public function setLinkIdForCaption();

    public function setLinkTagForImg() {
        $this->linkTagForImg = $this->setLinkTag($this->linkIdForImg);
        return $this->linkTagForImg;
    }

    public function setLinkTagForCaption() {
        $this->linkTagForCaption = $this->setLinkTag($this->linkIdForCaption);
        return $this->linkIdForCaption;
    }

    private function setLinkTag($linkId) {
        $linkTag =
            '<a href="' . $this->linkHref . '"'
            . ' id="' . $linkId . '"'
            . ($this->linkOnClick ? (' onclick="' . $this->linkOnClick . '"') : '')
            . ($this->linkClass ? (' class="' . $this->linkClass . '"') : '')
            . ($this->linkRel ? (' rel="' . $this->linkRel . '"') : '')
            . ($this->linkTitle ? (' title="' . $this->linkTitle . '"') : '')
            . '>';
        return $linkTag;
    }

    abstract public function setCaption();

    public function setCombinedTags() {
        $this->combinedTags = $this->linkTagForImg . $this->imgTag;

        if ($this->linkTagForImg) {
            $this->combinedTags .= '</a>';
        }

        if ($this->caption) {
            $this->combinedTags .= $this->caption . PHP_EOL;
        }

        return $this->combinedTags;
    }

    public function incrementSessionIdCounter() {
        $thumbnailCounter = $this->sessionManager->getThumbnailCounter();
        $this->sessionManager->setThumbnailCounter(++$thumbnailCounter);
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
                if ($photoData['takenTimestamp'])
                    $exifParts[] = $this->formatDateForCaption($photoData['takenTimestamp']);
                break;
            case 'none':
                break;
            case 'all':
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
        }

        if (!empty($exifParts)) {
            $exifCaption = '<span class="shashinCaptionExif">';
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
