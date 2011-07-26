<?php

class Public_ShashinLayoutManager {
    private $settings;
    private $settingsValues;
    private $container;
    private $collection;
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

    public function __construct(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        $this->settingsValues = $settings->get();
    }

    public function run(
      Lib_ShashinContainer $container,
      array $shortcode,
      array $collection,
      array $thumbnailCollection = null) {
        $this->container = $container;
        $this->shortcode = $shortcode;
        $this->collection = $collection;
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

    public function setTableCaptionTag() {
        if ($this->shortcode['description'] == 'y') {
            $this->tableCaptionTag =  '<caption>work in progress</caption>' . PHP_EOL;
        }

        return $this->tableCaptionTag;
    }

    public function setTableBody() {
        $cellCount = 1;
        $this->tableBody = '';

        for ($i = 0; $i < count($this->collection); $i++) {
            if ($cellCount == 1) {
                $this->tableBody .=  '<tr>' . PHP_EOL;
            }

            $photoDisplayer = $this->container->getPhotoDisplayer($this->collection[$i], $this->thumbnailCollection[$i]);
            $linkAndImageTags = $photoDisplayer->run($this->shortcode['size'], $this->shortcode['crop']);
            $imgWidth = $photoDisplayer->getImgWidth();
            $cellWidth = $imgWidth + $this->settingsValues['thumbPadding'];
            $this->tableBody .= '<td><div class="shashin3alpha_thumb_div" style="width: ' . $cellWidth . 'px;">';
            $this->tableBody .= $linkAndImageTags;

            if ($this->shortcode['caption'] == 'y') {
                $this->tableBody .=
                        '<span class="shashin_caption">'
                        . $this->collection[$i]->description
                        . '</span>';
            }

            $this->tableBody.= '</div></td>' . PHP_EOL;
            $cellCount++;

            if ($cellCount > $this->shortcode['columns'] || $i == (count($this->collection) - 1)) {
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
