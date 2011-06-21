<?php

class Public_ShashinShortcodeTransformer {
    private $shortcode;
    private $dataObjectSet;
    private $defaults = array(
        'type' => 'photos',
        'keys' => '',
        'size' => 'small',
        'format' => 'table',
        'caption' => 'n',
        'count' => '',
        'order' => 'natural',
        'position' => '',
        'clear' => '',
        'thumbnails' => ''
    );

    public function __construct(array $shortcode) {
        $this->shortcode = $shortcode;
    }

    public function setDataObjectSet(Lib_ShashinDataObjectCollection $dataObjectSet) {
        $this->dataObjectSet = $dataObjectSet;
    }

    public function getShortcode() {
        return $this->shortcode;
    }

    public function cleanShortcode() {
        array_walk($this->shortcode, array('ToppaFunctions', 'trimCallback'));
        array_walk($this->shortcode, array('ToppaFunctions', 'strtolowerCallback'));
        return $this->shortcode;
    }

    public function run() {
        $this->assignDefaultValuesIfEmpty();
        $this->dataObjectSet->setTagType($this->shortcode['type']);
        $this->dataObjectSet->setKeysString($this->shortcode['keys']);
        $this->dataObjectSet->setThumbnailSize($this->shortcode['size']);
        $this->dataObjectSet->setHowManyPhotos($this->shortcode['count']);
        $this->dataObjectSet->setOrderBy($this->shortcode['order']);
        $this->dataObjectSet->setThumbnailsKeysString($this->shortcode['thumbnails']);
        //return $htmlForPhotos;
    }

    public function assignDefaultValuesIfEmpty() {
        foreach ($this->defaults as $k=>$v) {
            if (!$this->shortcode[$k]) {
                $this->shortcode[$k] = $v;
            }
        }
    }
}