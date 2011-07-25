<?php

class Public_ShashinLayoutManager {
    private $container;
    private $dataObjectCollection;
    private $thumbnailCollection;
    private $shortcode;
    private $openingTableTag;
    private $tableCaptionTag;
    private $tableBody;
    private $combinedTags;


    protected $validInputValues = array(
        'caption' => array(null, 'y', 'n', 'c'),
        'description' => array(null, 'y', 'n'),
        'location' => array(null, 'y', 'n'),
        'position' => array(null, 'left', 'right', 'none', 'inherit', 'center'),
        'clear' => array(null, 'left', 'right', 'none', 'both', 'inherit')
    );

    public function __construct() {
    }

    public function run(
      Lib_ShashinContainer $container,
      array $shortcode,
      array $dataObjectCollection,
      array $thumbnailCollection = null) {
        $this->container = $container;
        $this->shortcode = $shortcode;
        $this->dataObjectCollection = $dataObjectCollection;
        $this->thumbnailCollection = $thumbnailCollection;
        $this->validateShortcodeLayout();
        $this->setOpeningTableTag();
        $this->setTableCaptionTag();
        $this->setTableBody();
        $this->setCombinedTags();
        return $this->combinedTags;
    }

    public function validateShortcodeLayout() {
        $this->isInListOfValidValues('caption', $this->shortcode['caption']);
        $this->isInListOfValidValues('description', $this->shortcode['description']);
        $this->isInListOfValidValues('location', $this->shortcode['location']);
        $this->isInListOfValidValues('position', $this->shortcode['position']);
        $this->isInListOfValidValues('clear', $this->shortcode['clear']);
    }

    private function isInListOfValidValues($shortcodeKey, $value) {
        if (!in_array($value, $this->validInputValues[$shortcodeKey])) {
            throw new Exception($value . __(" is not a valid ") . $shortcodeKey . __(" value"));
        }

        return true;
    }

    public function setOpeningTableTag() {
        $this->openingTableTag = '<table class="shashin_thumbs_table"';

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

    public function setTableCaptionTag() {
        if ($this->shortcode['description'] == 'y') {
            $this->tableCaptionTag =  '<caption>work in progress</caption>' . PHP_EOL;
        }

        return $this->tableCaptionTag;
    }

    public function setTableBody() {
        $cellCount = 1;
        $this->tableBody = '';

        for ($i = 0; $i < count($this->dataObjectCollection); $i++) {
            if ($cellCount == 1) {
                $this->tableBody .=  '<tr>' . PHP_EOL;
            }

            $photoDisplayer = $this->container->getPhotoDisplayer($this->dataObjectCollection[$i], $this->thumbnailCollection[$i]);
            $this->tableBody .= '<td>';
            $this->tableBody .= $photoDisplayer->run($this->shortcode['size'], $this->shortcode['crop']);

            if ($this->shortcode['caption'] == 'y') {
                $this->tableBody .=
                        '<span class="shashin_caption">'
                        . $this->dataObjectCollection[$i]->description
                        . '</span>';
            }

            $this->tableBody.= '</td>' . PHP_EOL;
            $cellCount++;

            if ($cellCount > $this->shortcode['columns'] || $i == (count($this->dataObjectCollection) - 1)) {
                $this->tableBody .= '</tr>' . PHP_EOL;
                $cellCount = 1;
            }
        }

        return $this->tableBody;
    }

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
