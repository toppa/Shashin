<?php

abstract class Public_ShashinDataObjectDisplayer {
    protected $settings;
    protected $shortcode;
    protected $functionsFacade;
    protected $dataObject;
    protected $thumbnail;
    protected $sessionManager;
    protected $albumIdForAjaxPhotoDisplay;
    protected $actualThumbnailSize;
    protected $displayThumbnailSize;
    protected $actualExpandedSize;
    protected $displayCropped = false;
    protected $imgWidth;
    protected $imgSrc;
    protected $imgAlt;
    protected $imgTitle;
    protected $imgClass;
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
    protected $expandedSizesMap = array();

    public function __construct() {
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setShortcode(Public_ShashinShortcode $shortcode) {
        $this->shortcode = $shortcode;
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
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

    public function setAlbumIdForAjaxPhotoDisplay($albumIdForAjaxPhotoDisplay = null) {
        $this->albumIdForAjaxPhotoDisplay = $albumIdForAjaxPhotoDisplay;
        return $this->albumIdForAjaxPhotoDisplay;
    }

    public function run() {
        $this->initializeSessionIdCounter();
        $requestedSize = $this->shortcode->size ? $this->shortcode->size : 'xsmall';
        $this->setDisplayThumbnailSize($requestedSize);
        $this->setActualThumbnailSize();
        $this->setActualExpandedSize();
        $this->setDisplayCropped();
        $this->setImgWidth();
        $this->setImgSrc();
        $this->setImgAlt();
        $this->setImgTitle();
        $this->setImgClass();
        $this->setImgTag();

        if ($this->dataObject->isVideo()) {
            $this->setLinkHrefVideo();
            $this->setLinkOnClickVideo();
            $this->setLinkRelVideo();
            $this->setLinkClassVideo();
            $this->setLinkTitleVideo();
        }

        else {
            $this->setLinkHref();
            $this->setLinkOnClick();
            $this->setLinkRel();
            $this->setLinkClass();
            $this->setLinkTitle();
        }

        $this->setLinkIdForImg();
        $this->setLinkIdForCaption();
        $this->setLinkTagForImg();
        $this->setLinkTagForCaption();
        $this->setCaption();
        $this->setCombinedTags();
        $this->incrementSessionIdCounter();
        return $this->combinedTags;
    }

    public function initializeSessionIdCounter() {
        if (!$this->sessionManager->getThumbnailCounter()) {
            $this->sessionManager->setThumbnailCounter(1);
        }
    }

    public function setDisplayThumbnailSize($requestedSize = 'xsmall') {
        if (is_numeric($requestedSize)) {
            $this->displayThumbnailSize = $requestedSize;
        }

        elseif ($requestedSize == 'max') {
            $this->displayThumbnailSize = floor($this->settings->themeMaxSize / $this->shortcode->columns);
            $this->displayThumbnailSize -= 10; // guess for padding/margins per image
        }

        else {
            $this->displayThumbnailSize = $this->shortcode->mapStringSizeToNumericSize($requestedSize);
        }

        if (!is_numeric($this->displayThumbnailSize)) {
            throw New Exception(__('invalid size requested', 'shashin'));
        }

        return $this->displayThumbnailSize;
    }

    public function getDisplayThumbnailSize() {
        return $this->displayThumbnailSize;
    }

    public function setActualThumbnailSize() {
        for ($i = 0; $i < count($this->validThumbnailSizes); $i++) {
            if ($this->validThumbnailSizes[$i] >= $this->displayThumbnailSize) {
                $this->actualThumbnailSize = $this->validThumbnailSizes[$i];
                break;
            }
        }

        if (!$this->actualThumbnailSize) {
            $this->actualThumbnailSize = $this->validThumbnailSizes[0];
        }

        return $this->actualThumbnailSize;
    }

    abstract public function setActualExpandedSize();

    public function setDisplayCropped() {
        if ($this->shortcode->crop == 'y' && in_array($this->actualThumbnailSize, $this->validCropSizes)) {
            $this->displayCropped = true;
        }

        return $this->displayCropped;
    }

    public function setImgWidth() {
        if ($this->displayCropped || ($this->thumbnail->width > $this->thumbnail->height)) {
            $this->imgWidth = $this->displayThumbnailSize;
        }

        elseif ($this->thumbnail->height > $this->thumbnail->width) {
            $percentage = $this->displayThumbnailSize / $this->thumbnail->height;
            $this->imgWidth = $percentage * $this->thumbnail->width;
            settype($this->imgWidth, 'int');
        }

        else {
            $this->imgWidth = null;
        }

        return $this->imgWidth;
    }

    abstract public function setImgSrc();
    abstract public function setImgAlt();
    abstract public function setImgTitle();

    public function setImgClass() {
        $this->imgClass = 'shashinThumbnailImage';
        return $this->imgClass;
    }

    public function setImgTag() {
        $this->imgTag =
            '<img src="' . $this->imgSrc . '"'
            . ' alt="' . $this->imgAlt . '"'
            . ($this->imgTitle ? (' title="' . $this->imgTitle . '"') : '')
            . ($this->imgWidth ? (' width="' . $this->imgWidth . '"') : '')
            . ' class="' . $this->imgClass . '"'
            . ' id="shashinThumbnailImage_' . $this->sessionManager->getThumbnailCounter() . '" />';
        return $this->imgTag;
    }

    abstract public function setLinkHref();
    abstract public function setLinkHrefVideo();

    public function setLinkOnClick() {
        $this->linkOnClick = null;
        return $this->linkOnClick;
    }

    public function setLinkOnClickVideo() {
        $this->linkOnClick = null;
        return $this->linkOnClick;
    }

    public function setLinkRel() {
        $this->linkRel = null;
        return $this->linkRel;
    }

    public function setLinkRelVideo() {
        $this->linkRel = null;
        return $this->linkRel;
    }

    public function setLinkClass() {
        $this->linkClass = null;
        return $this->linkClass;
    }

    public function setLinkClassVideo() {
        $this->linkClass = null;
        return $this->linkClass;
    }

    public function setLinkTitle() {
        $this->linkTitle = null;
        return $this->linkTitle;
    }

    public function setLinkTitleVideo() {
        $this->linkTitle = null;
        return $this->linkTitle;
    }

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

    abstract public function setCaption();

    private function setLinkTag($linkId) {
        $linkTag =
            '<a href="' . $this->linkHref . '"'
            . ' id="' . $linkId . '"'
            . ' data-'
                . preg_replace('/.*_/', '', get_class($this->dataObject))
                . '="'
                . $this->dataObject->id
                . '"'
            . ($this->linkOnClick ? (' onclick="' . $this->linkOnClick . '"') : '')
            . ($this->linkClass ? (' class="' . $this->linkClass . '"') : '')
            . ($this->linkRel ? (' rel="' . $this->linkRel . '"') : '')
            . ($this->linkTitle ? (' title="' . $this->linkTitle . '"') : '')
            . '>';
        return $linkTag;
    }

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

    public function getExpandedSizesMap() {
        return $this->expandedSizesMap;
    }

    abstract public function setExifDataForCaption();
    abstract public function setDateForCaption($date = null);
    abstract public function adjustVideoDimensions();

    public function makeImgSrcProtocolConsistent() {
        // return $url as http if not on secure connection, so Shashin can work with Pinterest
        if (!$_SERVER["HTTPS"] && substr(strtolower($this->imgSrc), 0, 5) == "https") {
            $this->imgSrc = "http" . substr($this->imgSrc, 5);
        }

        return $this->imgSrc;
    }
}
