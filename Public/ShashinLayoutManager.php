<?php

abstract class Public_ShashinLayoutManager {
    protected $settings;
    protected $settingsValues;
    protected $functionsFacade;
    protected $container;
    protected $collection;
    protected $thumbnailCollection;
    protected $shortcode;
    protected $openingTableTag;
    protected $tableCaptionTag;
    protected $tableBody;
    protected $combinedTags;
    protected $validInputValues = array(
        'caption' => array(null, 'y', 'n', 'c'),
        'position' => array(null, 'left', 'right', 'none', 'inherit', 'center'),
        'clear' => array(null, 'left', 'right', 'none', 'both', 'inherit')
    );

    public function __construct(
      Lib_ShashinSettings $settings,
      ToppaFunctionsFacade $functionsFacade) {
        $this->settings = $settings;
        $this->settingsValues = $settings->get();
        $this->functionsFacade = $functionsFacade;
    }

    public function run(
      Lib_ShashinContainer $container,
      array $shortcode,
      array $collection,
      array $thumbnailCollection = null) {
        try {
            $this->container = $container;
            $this->shortcode = $shortcode;
            $this->collection = $collection;
            $this->thumbnailCollection = $thumbnailCollection;
            $this->validateShortcodeLayout();
            $this->setOpeningTableTag();
            $this->setPhotoTableCaptionTag();
            $this->setTableBody();
            $this->setCombinedTags();
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->combinedTags;
    }

    public function validateShortcodeLayout() {
        $this->isInListOfValidValues('caption', $this->shortcode['caption']);
        $this->isInListOfValidValues('position', $this->shortcode['position']);
        $this->isInListOfValidValues('clear', $this->shortcode['clear']);
    }

    protected function isInListOfValidValues($shortcodeKey, $value) {
        if (!in_array($value, $this->validInputValues[$shortcodeKey])) {
            throw new Exception($value . __(" is not a valid ") . $shortcodeKey . __(" value"));
        }

        return true;
    }

    public function setOpeningTableTag() {
        $this->openingTableTag = '<table class="shashin3alpha_thumbs_table"';

        if ($this->shortcode['position'] || $this->shortcode['clear']) {
            $this->openingTableTag .= $this->addStyleForOpeningTableTag();
        }

        $this->openingTableTag .= '>' . PHP_EOL;
        return $this->openingTableTag;
    }

    public function addStyleForOpeningTableTag() {
        $style = ' style="';

        if ($this->shortcode['position'] == 'center') {
            $style .= 'margin-left: auto; margin-right: auto;';
        }

        else if ($this->shortcode['position']) {
            $style .= 'float: '. $this->shortcode['position'] . ';"';
        }

        if ($this->shortcode['clear']) {
            $style .=  'clear: ' . $this->shortcode['clear'] . ';"';
        }

        $style .= '"';
        return $style;
    }

    abstract public function setPhotoTableCaptionTag();

    public function setTableBody() {
        $cellCount = 1;
        $this->tableBody = '';

        for ($i = 0; $i < count($this->collection); $i++) {
            if ($cellCount == 1) {
                $this->tableBody .=  '<tr>' . PHP_EOL;
            }

            $dataObjectDisplayer = $this->container->getDataObjectDisplayer($this->collection[$i], $this->thumbnailCollection[$i]);
            $linkAndImageTags = $dataObjectDisplayer->run($this->shortcode['size'], $this->shortcode['crop']);
            $imgWidth = $dataObjectDisplayer->getImgWidth();
            $cellWidth = $imgWidth + $this->settingsValues['thumbPadding'];
            $this->tableBody .= '<td><div class="shashin3alpha_thumb_div" style="width: ' . $cellWidth . 'px;">';
            $this->tableBody .= $linkAndImageTags;
            $linkTag = $dataObjectDisplayer->getATag();
            $this->tableBody .= $this->generateCaption($this->collection[$i], $linkTag);
            $this->tableBody.= '</div></td>' . PHP_EOL;
            $cellCount++;

            if ($cellCount > $this->shortcode['columns'] || $i == (count($this->collection) - 1)) {
                $this->tableBody .= '</tr>' . PHP_EOL;
                $cellCount = 1;
            }
        }

        return $this->tableBody;
    }

    abstract public function generateCaption(Lib_ShashinDataObject $dataObject, $linkTag = null);

    public function setCombinedTags() {
        $this->combinedTags =
                $this->openingTableTag
                . $this->tableCaptionTag
                . $this->tableBody
                . '</table>'
                . PHP_EOL;
        return $this->combinedTags;
    }
}
