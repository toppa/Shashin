<?php

class ShashinShortcodeTransformer {
    private $shortcode;
    private $defaults = array(
        'type' => 'photos',
        'keys' => '',
        'size' => 'small',
        'format' => 'table',
        'caption' => 'n',
        'count' => '',
        'order' => 'server',
        'position' => '',
        'clear' => '',
        'thumbnails' => ''
    );

    public function __construct($shortcode) {
        $this->shortcode = $shortcode;
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
        switch ($this->shortcode['type']) {
            case 'photos':
                $htmlForPhotos = $this->getHtmlForPhotoKeys();
        }

        return $htmlForPhotos;
    }

    public function assignDefaultValuesIfEmpty() {
        foreach ($this->defaults as $k=>$v) {
            if (!$this->shortcode[$k]) {
                $this->shortcode[$k] = $v;
            }
        }
    }

    public function getHtmlForPhotoKeys() {

    }
}