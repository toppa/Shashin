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
    private $sessionManager;
    private $slideshowJs;
    private $totalTables = 1;
    private $currentDataObjectDisplayer;
    private $numericColumns;
    private $currentTableNumber;
    private $startingTableGroupCounter;
    private $endingTableGroupCounter;
    private $currentTableId;
    private $startTableWithThisPhoto = 0;
    private $endTableWithThisPhoto;
    private $tableCellCount = 1;
    private $openingTableTag;
    private $tableCaptionTag;
    private $tableBody;
    private $slideshowGroupCounter;
    private $combinedTags;

    public function __construct() {
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
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

    public function setSessionManager(Public_ShashinSessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
        return $this->sessionManager;
    }

    public function setSlideshowJs($slideshowJs) {
        $this->slideshowJs = $slideshowJs;
        return $this->slideshowJs;
    }

    public function run() {
        $this->setThumbnailCollectionIfNeeded();
        $this->setCollection();
        $this->initializeSessionGroupCounter();
        $this->setTotalTables();

        for ($this->currentTableNumber = 1; $this->currentTableNumber <= $this->totalTables; $this->currentTableNumber++) {
            $this->setStartingAndEndingTableGroupCounter();
            $this->setCurrentTableId();
            $this->setOpeningTableTag();
            $this->setTableCaptionTag();
            $this->setTableBody();
            $this->setSlideshowGroupCounter();
            $this->setCombinedTags();
            $this->incrementSessionGroupCounter();
        }

        return '<div class="shashinPhotoGroups">' . $this->combinedTags . '</div>' . PHP_EOL;
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

        if (empty($this->collection)) {
            throw New Exception(__('No photos found for specified shortcode', 'shashin'));
        }

        return $this->collection;
    }

    public function initializeSessionGroupCounter() {
        if (!$this->sessionManager->getGroupCounter() && isset($this->request['shashinParentTableId'])) {
            $this->sessionManager->setGroupCounter($this->request['shashinParentTableId']);
        }

        elseif (!$this->sessionManager->getGroupCounter()) {
            $this->sessionManager->setGroupCounter(1);
        }

        return $this->sessionManager->getGroupCounter();
    }

    public function setTotalTables() {
        if ($this->shortcode->type != 'album'
          && (count($this->collection) > $this->settings->defaultPhotoLimit)) {
            $this->totalTables = ceil(
                count($this->collection)
                / $this->settings->defaultPhotoLimit
            );
        }

        return $this->totalTables;
    }

    public function setStartingAndEndingTableGroupCounter() {
        if ($this->currentTableNumber == 1) {
            $this->startingTableGroupCounter = $this->sessionManager->getGroupCounter();
            $this->endingTableGroupCounter = $this->sessionManager->getGroupCounter() + $this->totalTables - 1;
        }
    }

    public function setCurrentTableId() {
        $this->currentTableId = 'shashinGroup_' . $this->sessionManager->getGroupCounter();

        if ($this->shortcode->type != 'album') {
             $this->currentTableId .= '_' . $this->startingTableGroupCounter;
        }

        return $this->currentTableId;
    }

    public function setOpeningTableTag() {
        $this->openingTableTag = '<div class="'
            . ($this->settings->thumbnailDisplay == 'square' ? 'shashinTableSquare ' : '')
            . 'shashinThumbnailsTable" id="' . $this->currentTableId . '"'
            . $this->addStyleForOpeningTableTag()
            . '>'
            . PHP_EOL;
        return $this->openingTableTag;
    }

    public function addStyleForOpeningTableTag() {
        $style = ' style="';
        $style .= $this->addStylePositionAndMarginIfNeeded();
        $style .= $this->addStyleClearIfNeeded();
        $style .= $this->addStyleDisplayIfNeeded();
        $style .= '"';
        return $style;
    }

    public function addStylePositionAndMarginIfNeeded() {
        if ($this->shortcode->position == 'center') {
            return 'margin-left: auto; margin-right: auto;';
        }

        else if ($this->shortcode->position) {
            return 'float: '
                . $this->shortcode->position
                . '; '
                . ($this->shortcode->position == 'left' ? 'margin-right: 6px;' : 'margin-left: 6px');
        }

        return null;
    }

    public function addStyleClearIfNeeded() {
        if ($this->shortcode->clear) {
            return 'clear: ' . $this->shortcode->clear . ';';
        }

        return null;
    }

    public function addStyleDisplayIfNeeded() {
        if ($this->currentTableNumber > 1) {
            return 'display: none;';
        }

        return null;
    }

    public function setTableCaptionTag() {
        $navLinks = array();
        $mayNeedPreviousAndNext = $this->isPreviousOrNextLinkNeeded();
        $navLinks = $this->addPreviousLinkIfNeeded($mayNeedPreviousAndNext, $navLinks);
        $navLinks = $this->addReturnLinkIfNeeded($navLinks);
        $navLinks = $this->addNextLinkIfNeeded($mayNeedPreviousAndNext, $navLinks);

        $this->tableCaptionTag = '<div class="shashinCaption">';
        $this->tableCaptionTag .= $this->addAlbumTitleIfNeeded();
        $this->tableCaptionTag .= $this->addNavLinksIfNeeded($navLinks);
        $this->tableCaptionTag .= '</div>' . PHP_EOL;
        return $this->tableCaptionTag;
    }

    public function isPreviousOrNextLinkNeeded() {
        if ($this->totalTables > 1
          && $this->sessionManager->getGroupCounter() >= $this->startingTableGroupCounter
          && $this->sessionManager->getGroupCounter() <= $this->endingTableGroupCounter) {
            return true;
        }

        return false;
    }

    public function addPreviousLinkIfNeeded($mayNeedPreviousAndNext, $navLinks) {
        if ($mayNeedPreviousAndNext && ($this->sessionManager->getGroupCounter() > $this->startingTableGroupCounter)) {
            $navLinks[] = '<a href="#" class="shashinPrevious">&laquo; ' . __('Previous', 'shashin') . '</a>';
        }

        return $navLinks;
    }

    public function addReturnLinkIfNeeded($navLinks) {
        if (isset($this->request['shashinParentTableId'])) {
            $navLinks[] = '<a href="#" class="shashinReturn" id="shashinReturn_'
                . $this->request['shashinParentTableId']
                . '_'
                . $this->request['shashinAlbumId']
                . '">'
                .  __('Return', 'shashin')
                . '</a>';
        }

        return $navLinks;
    }

    public function addNextLinkIfNeeded($mayNeedPreviousAndNext, $navLinks) {
        if ($mayNeedPreviousAndNext && ($this->sessionManager->getGroupCounter() < $this->endingTableGroupCounter)) {
            $navLinks[] = '<a href="#" class="shashinNext">' .  __('Next', 'shashin') . ' &raquo;</a>';
        }

        return $navLinks;
    }

    public function addAlbumTitleIfNeeded() {
        $albumTitle = '';

        if (isset($this->request['shashinParentAlbumTitle'])) {
            $albumTitle =
                '<strong>'
                . htmlentities(stripslashes($this->request['shashinParentAlbumTitle']), ENT_COMPAT, 'UTF-8')
                . '</strong><br />';
        }

        return $albumTitle;
    }

    public function addNavLinksIfNeeded($navLinks) {
        if (!empty($navLinks)) {
            return implode(' | ', $navLinks);
        }

        return null;
    }

    public function setTableBody() {
        $this->setEndTableWithThisPhoto();
        $this->setNumericColumnsIfNeeded();
        $this->tableBody = '';

        for ($i = $this->startTableWithThisPhoto; $i <= $this->endTableWithThisPhoto; $i++) {
            $this->tableBody .= $this->startTableRowIfNeeded();
            $this->getDataObjectDisplayerForThisCell($i);
            $this->tableBody .= $this->addTableCell();
            $this->tableBody .= $this->closeTableRowIfNeeded($i);
            $this->setTableCellCount($i);
            $this->setStartTableWithThisPhotoIfNeeded($i);
        }

        return $this->tableBody;
    }

    public function setEndTableWithThisPhoto() {
        $zeroBasedCountOfCollection = count($this->collection) - 1;

        if ($this->shortcode->type == 'album') {
            $possibleEndingPhoto = $zeroBasedCountOfCollection;;
        }

        else {
            $possibleEndingPhoto = $this->startTableWithThisPhoto + $this->settings->defaultPhotoLimit - 1;
        }

        if ($zeroBasedCountOfCollection < $possibleEndingPhoto) {
             $this->endTableWithThisPhoto = $zeroBasedCountOfCollection;
        }

        else {
            $this->endTableWithThisPhoto = $possibleEndingPhoto;
        }

        return $this->endTableWithThisPhoto;
    }

    public function setNumericColumnsIfNeeded() {
        if (is_numeric($this->shortcode->columns)) {
            $maxPossibleColumns = $this->shortcode->columns;
        }

        elseif ($this->shortcode->columns == 'max') {
            if (!is_numeric($this->shortcode->size)) {
                $thumbnailSize = $this->shortcode->mapStringSizeToNumericSize($this->shortcode->size);
            }

            else {
                $thumbnailSize = $this->shortcode->size;
            }

            // guess 10px for padding/margins
            $columns = $this->settings->themeMaxSize / ($thumbnailSize + 10);
            $maxPossibleColumns = floor($columns) ? floor($columns) : 1;
        }


        else {
            $maxPossibleColumns = 1;
        }


        // make sure the calculated number of columns isn't greater than
        // the total number of photos
        if ($maxPossibleColumns > count($this->collection)) {
            $this->numericColumns = count($this->collection);
        }

        else {
            $this->numericColumns = $maxPossibleColumns;
        }

        return $this->numericColumns;
    }

    public function startTableRowIfNeeded() {
        if ($this->tableCellCount == 1) {
            return '<div class="shashinTableRow">' . PHP_EOL;
        }

        return null;
    }

    public function getDataObjectDisplayerForThisCell($i) {
        $alternateThumbnail = $this->getAlternateThumbnailIfNeeded($i);
        $albumId = isset($this->request['shashinAlbumId']) ? $this->request['shashinAlbumId'] : null;
        $this->currentDataObjectDisplayer = $this->container->getDataObjectDisplayer(
            $this->shortcode,
            $this->collection[$i],
            $alternateThumbnail,
            null,
            $albumId
        );

        return $this->currentDataObjectDisplayer;
    }

    public function getAlternateThumbnailIfNeeded($i) {
        if (is_array($this->thumbnailCollection)) {
            return $this->thumbnailCollection[$i];
        }

        return null;
    }

    public function addTableCell() {
        $linkAndImageTags = $this->currentDataObjectDisplayer->run();
        $width = floor(100 / $this->numericColumns);
        $maxWidth = $this->currentDataObjectDisplayer->getImgWidth();
        $cell = '<div class="shashinTableCell"'
            . ' data-original_width="' . $width . '%"' // to recalculate the width if the browser is being resized
            . ' style=" width: ' . $width . '%;'
            . ' max-width: ' . ($maxWidth ? ($maxWidth . 'px;') : 'none')
            . '"><div class="shashinThumbnailWrapper">'
            . $linkAndImageTags
            . '</div></div>' . PHP_EOL;
        return $cell;
    }

    public function closeTableRowIfNeeded($i) {
        if ($this->atEndOfRow($i)) {
            return '</div><div class="shashinTableRowClear">&nbsp;</div>' . PHP_EOL;
        }

        return null;
    }

    public function setTableCellCount($i) {
        if ($this->atEndofRow($i)) {
            $this->tableCellCount = 1;
        }

        else {
            $this->tableCellCount++;
        }

        return $this->tableCellCount;
    }

    private function atEndOfRow($i) {
        return ($this->tableCellCount >= $this->numericColumns || $i == $this->endTableWithThisPhoto);
    }

    public function setStartTableWithThisPhotoIfNeeded($i) {
        if ($i == $this->endTableWithThisPhoto) {
            $this->startTableWithThisPhoto = $this->endTableWithThisPhoto + 1;
        }

        return $this->startTableWithThisPhoto;
    }

    public function setSlideshowGroupCounter() {
        $this->slideshowGroupCounter = null;

        if ($this->shortcode->type != 'album' && isset($this->slideshowJs)) {
            $groupNumber = $this->sessionManager->getGroupCounter();

            if (isset($this->request['shashinAlbumId'])) {
                $groupNumber .= '_' . $this->request['shashinAlbumId'];
            }

            $this->slideshowGroupCounter = $this->slideshowJs->run($groupNumber);
        }

        return $this->slideshowGroupCounter;
    }

    public function setCombinedTags() {
        $this->combinedTags .=
                $this->openingTableTag
                . $this->tableCaptionTag
                . $this->tableBody
                . str_replace('class="', 'class="shashinCaptionBottom ', $this->tableCaptionTag)
                . '</div>'
                . PHP_EOL
                . $this->slideshowGroupCounter;
        return $this->combinedTags;
    }

    public function incrementSessionGroupCounter() {
        $groupCounter = $this->sessionManager->getGroupCounter();
        $this->sessionManager->setGroupCounter(++$groupCounter);
    }
}
