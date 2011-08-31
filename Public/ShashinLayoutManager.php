<?php

class Public_ShashinLayoutManager {
    private $settings;
    private $functionsFacade;
    private $container;
    private $dataObjectCollection;
    private $collection;
    private $thumbnailDataObjectCollection;
    private $thumbnailCollection;
    private $shortcode;
    private $request;
    private $openingTableTag;
    private $tableCaptionTag;
    private $tableBody;
    private $groupCounter;
    private $combinedTags;

    public function __construct() {
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setContainer(Public_ShashinContainer $container) {
        $this->container = $container;
        return $this->container;
    }

    public function setShortcode(Public_ShashinShortcode $shortcode) {
        $this->shortcode = $shortcode;
        return $this->shortcode;
    }

    public function setDataObjectCollection(Lib_ShashinDataObjectCollection $dataObjectCollection) {
        $this->dataObjectCollection = $dataObjectCollection;
        return $this->dataObjectCollection;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function run() {
        $this->setThumbnailCollectionIfNeeded();
        $this->setCollection();
        $this->initializeSessionGroupCounter();
        $this->setOpeningTableTag();
        $this->setTableCaptionTag();
        $this->setTableBody();
        $this->setGroupCounterHtml();
        $this->setCombinedTags();
        $this->incrementSessionGroupCounter();
        return $this->combinedTags;
    }

    public function setThumbnailCollectionIfNeeded() {
        if ($this->shortcode->thumbnail) {
            $this->thumbnailDataObjectCollection = clone $this->dataObjectCollection;
            $this->thumbnailDataObjectCollection->setUseThumbnailId(true);
            $this->thumbnailCollection = $this->thumbnailDataObjectCollection->getCollectionForShortcode($this->shortcode);
        }

        return $this->thumbnailCollection;
    }

    public function setCollection() {
        $this->collection = $this->dataObjectCollection->getCollectionForShortcode($this->shortcode);
        return $this->collection;
    }

    public function initializeSessionGroupCounter() {
        if (!$_SESSION['shashinGroupCounter']) {
            $_SESSION['shashinGroupCounter'] = 1;
        }
    }

    public function setOpeningTableTag() {
        $this->openingTableTag = '<table class="shashin3alpha_thumbs_table" id="shashinGroup'
            . $_SESSION['shashinGroupCounter'] . '"';

        if ($this->shortcode->position || $this->shortcode->clear) {
            $this->openingTableTag .= $this->addStyleForOpeningTableTag();
        }

        $this->openingTableTag .= '>' . PHP_EOL;
        return $this->openingTableTag;
    }

    public function addStyleForOpeningTableTag() {
        $style = ' style="';

        if ($this->shortcode->position == 'center') {
            $style .= 'margin-left: auto; margin-right: auto;';
        }

        else if ($this->shortcode->position) {
            $style .= 'float: '. $this->shortcode->position . ';"';
        }

        if ($this->shortcode->clear) {
            $style .=  'clear: ' . $this->shortcode->clear . ';"';
        }

        $style .= '"';
        return $style;
    }

    public function setTableCaptionTag() {
        if (is_numeric($this->request['shashinFinalPage'])) {
            $finalPage = $this->request['shashinFinalPage'];
        }

        elseif (!$this->dataObjectCollection->getMayNeedPagination()) {
            return null;
        }

        elseif ($this->dataObjectCollection->getCount() < $this->settings->photosPerPage) {
            return null;
        }

        else {
            $finalPage = ceil(
                $this->dataObjectCollection->getCount()
                / $this->settings->photosPerPage
            );
        }

        $this->tableCaptionTag = '<caption>';

        if (is_numeric($this->request['shashinPage'])) {
            $currentPage = $this->request['shashinPage'];
        }

        else {
            $currentPage = 1;
        }

        $permalink = $this->functionsFacade->getPermalink();
        $queryStringGlue = strpos($permalink, '?') ? '&amp;' : '?';
        $firstHalfOfLink =  $permalink . $queryStringGlue;

        if ($currentPage > 1) {
            $previousLink =
                '<a href="' . $firstHalfOfLink
                . 'shashinPage=' . ($currentPage - 1)
                . '&amp;shashinFinalPage=' . $finalPage
                . '">&laquo; ' . __('Previous', 'shashin') . '</a>';
        }

        if ($currentPage < $finalPage) {
            $nextLink =
                '<a href="' . $firstHalfOfLink
                . 'shashinPage=' . ($currentPage + 1)
                . '&amp;shashinFinalPage=' . $finalPage
                . '">' . __('Next', 'shashin') . ' &raquo;</a>';

        }

        if ($previousLink) {
            $this->tableCaptionTag .= $previousLink;
        }

        if ($previousLink && $nextLink) {
            $this->tableCaptionTag .= ' | ';
        }

        if ($nextLink) {
            $this->tableCaptionTag .= $nextLink;
        }

        $this->tableCaptionTag .= '</caption>' . PHP_EOL;
        return $this->tableCaptionTag;
    }

    public function setTableBody() {
        $cellCount = 1;
        $this->tableBody = '';

        for ($i = 0; $i < count($this->collection); $i++) {
            if ($cellCount == 1) {
                $this->tableBody .=  '<tr>' . PHP_EOL;
            }

            $currentThumbnailCollection = null;

            if (is_array($this->thumbnailCollection)) {
                $currentThumbnailCollection = $this->thumbnailCollection[$i];
            }

            $dataObjectDisplayer = $this->container->getDataObjectDisplayer(
                $this->shortcode,
                $this->collection[$i],
                $currentThumbnailCollection
            );
            $linkAndImageTags = $dataObjectDisplayer->run();
            $imgWidth = $dataObjectDisplayer->getImgWidth();
            $cellWidth = $imgWidth + $this->settings->thumbPadding;
            $this->tableBody .= '<td><div class="shashin3alpha_thumb_div" style="width: ' . $cellWidth . 'px;">';
            $this->tableBody .= $linkAndImageTags;
            $this->tableBody.= '</div></td>' . PHP_EOL;
            $cellCount++;

            if ($cellCount > $this->shortcode->columns || $i == (count($this->collection) - 1)) {
                $this->tableBody .= '</tr>' . PHP_EOL;
                $cellCount = 1;
            }
        }

        return $this->tableBody;
    }

    public function setGroupCounterHtml() {
        if (($this->shortcode->type == 'photo' || $this->shortcode->type == 'albumphotos')
          && $this->settings->imageDisplay == 'highslide') {

            $this->groupCounter = '<script type="text/javascript">'
                . "addHSSlideshow('group" . $_SESSION['shashinGroupCounter'] . "');</script>"
                . PHP_EOL;
        }

        else {
            $this->groupCounter = null;
        }

        return $this->groupCounter;
    }

    public function setCombinedTags() {
        $this->combinedTags =
                $this->openingTableTag
                . $this->tableCaptionTag
                . $this->tableBody
                . '</table>'
                . PHP_EOL
                . $this->groupCounter;
        return $this->combinedTags;
    }

    public function incrementSessionGroupCounter() {
        $_SESSION['shashinGroupCounter']++;
    }
}
