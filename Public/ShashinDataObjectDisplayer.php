<?php

abstract class Public_ShashinDataObjectDisplayer {
    protected $settings;
    protected $shortcode;
    protected $functionsFacade;
    protected $dataObject;
    protected $thumbnail;
    protected $sessionManager;
    protected $albumIdForAjaxHighslideDisplay;
    protected $actualThumbnailSize;
    protected $displayThumbnailSize;
    protected $actualExpandedSize;
    protected $displayCropped = false;
    protected $imgHeight;
    protected $imgWidth;
    protected $imgSrc;
    protected $imgAlt;
    protected $imgTitle;
    protected $imgClass;
    protected $imgStyle;
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
    protected $thumbnailSizesMap = array(
        'xsmall' => 72,
        'small' => 150,
        'medium' => 300,
        'large' => 600,
        'xlarge' => 800,
    );

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

    public function setAlbumIdForAjaxHighslideDisplay($albumIdForAjaxHighslideDisplay = null) {
        $this->albumIdForAjaxHighslideDisplay = $albumIdForAjaxHighslideDisplay;
        return $this->albumIdForAjaxHighslideDisplay;
    }

    public function run($albumIdForAjaxHighslideDisplay = null) {
        $this->initializeSessionIdCounter();
        $requestedSize = $this->shortcode->size ? $this->shortcode->size : 'xsmall';
        $this->setDisplayThumbnailSize($requestedSize);
        $this->setActualThumbnailSize();
        $this->setActualExpandedSize();
        $this->setDisplayCropped();
        $this->setImgWidthAndHeight();
        $this->setImgSrc();
        $this->setImgAlt();
        $this->setImgTitle();
        $this->setImgClass();
        $this->setImgStyle();
        $this->setImgTag();

        if ($this->dataObject->isVideo()) {
            $this->setLinkHrefVideo();
            $this->setLinkOnClickVideo($albumIdForAjaxHighslideDisplay);
            $this->setLinkRelVideo();
        }

        else {
            $this->setLinkHref();
            $this->setLinkOnClick($albumIdForAjaxHighslideDisplay);
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
        return $this->combinedTags;
    }

    public function initializeSessionIdCounter() {
        if (!$this->sessionManager->getThumbnailCounter()) {
            $this->sessionManager->setThumbnailCounter(1);
        }
    }

    public function setDisplayThumbnailSize($requestedSize = 'xsmall') {
        if (array_key_exists($requestedSize, $this->thumbnailSizesMap)) {
            $this->displayThumbnailSize = $this->thumbnailSizesMap[$requestedSize];
        }

        elseif ($requestedSize == 'max') {
            $this->displayThumbnailSize = floor($this->settings->themeMaxSize / $this->shortcode->columns);
            $this->displayThumbnailSize -= 10; // guess for padding/margins per image
        }

        else {
            $this->displayThumbnailSize = $requestedSize;
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

    public function setImgWidthAndHeight() {
        if ($this->displayCropped) {
            $this->imgWidth = $this->displayThumbnailSize;
            $this->imgHeight = $this->displayThumbnailSize;
        }

        elseif (!$this->thumbnail->width || !$this->thumbnail->height) {
            $this->imgWidth = null;
            $this->imgHeight = null;
        }

        elseif ($this->thumbnail->width > $this->thumbnail->height) {
            $this->imgWidth = $this->displayThumbnailSize;
            $percentage = $this->displayThumbnailSize / $this->thumbnail->width;
            $this->imgHeight = $percentage * $this->thumbnail->height;
            settype($this->imgHeight, 'int');
        }

        else {
            $this->imgHeight = $this->displayThumbnailSize;
            $percentage = $this->displayThumbnailSize / $this->thumbnail->height;
            $this->imgWidth = $percentage * $this->thumbnail->width;
            settype($this->imgWidth, 'int');
        }

        return array($this->imgWidth, $this->imgHeight);
    }

    abstract public function setImgSrc();
    abstract public function setImgAlt();
    abstract public function setImgTitle();

    public function makeTextQuotable($text) {
        // there may already be entities in the text, so we want to be very
        // conservative with what we replace
        return str_replace('"', '&quot;', $text);
    }

    public function setImgClass() {
        $this->imgClass = 'shashinThumbnailImage';
        return $this->imgClass;
    }

    // I'm not sure why, but when using max-width, we need to knock
    // a couple pixels off the padding to get it right (there's an extra 2px
    // coming from somewhere)
    public function setImgStyle() {
        if (!$this->imgWidth) {
            $this->imgStyle = 'max-width: '
                . $this->displayThumbnailSize . 'px; padding: '
                . floor(($this->settings->thumbPadding / 2) - 2)
                . 'px;';
        }

        return $this->imgStyle;
    }

    public function setImgTag() {
        $this->imgTag =
            '<img src="' . $this->imgSrc . '"'
            . ' alt="' . $this->imgAlt . '"'
            . ($this->imgTitle ? (' title="' . $this->imgTitle . '"') : '')
            . ($this->imgWidth ? (' width="' . $this->imgWidth . '"') : '')
            . ($this->imgHeight ? (' height="' . $this->imgHeight . '"') : '')
            . ($this->imgStyle ? (' style="' . $this->imgStyle . '"') : '')
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

    public function setLinkTitle() {
        $this->linkTitle = null;
        return $this->linkTitle;
    }

    public function setLinkClass() {
        $this->linkClass = null;
        return $this->linkClass;
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

    abstract public function formatExifDataForHighslideCaption();
    abstract public function formatDateForHighslideCaption($date = null);
}
