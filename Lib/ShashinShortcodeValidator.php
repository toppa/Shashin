<?php

class Public_ShashinShortcodeValidator {
    private $shortcode = array();
    private $validInputValues = array(
        'type' => array('photo', 'album'),
        'size' => array('', 'x-small', 'small', 'medium', 'large', 'max'),
        'format' => array('', 'table', 'list'),
        'caption' => array('', 'y', 'n', 'c'),
        'order' => array('', 'date', 'filename', 'title', 'location', 'random', 'source', 'user'),
        'reverse' => array('', 'y', 'n'),
        'position' => array('', 'left', 'right', 'none', 'inherit', 'center'),
        'clear' => array('', 'left', 'right', 'none', 'both', 'inherit')
    );

    public function __construct(array $shortcode) {
        $this->shortcode = $shortcode;
    }

    public function run() {
        try {
            $this->validateType();
            $this->validateId();
            $this->validateSize();
            $this->validateFormat();
            $this->validateCaption();
            $this->validateLimit();
            $this->validateOrder();
            $this->validateReverse();
            $this->validatePosition();
            $this->validateClear();
            $this->validateThumbnails();
        }

        catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function validateType() {
        return $this->isInListOfValidValues('type');
    }

    public function validateId() {
        return $this->isAStringOfNumbersOrNull($this->shortcode['id']);
    }

    public function validateSize() {
        if (ToppaFunctions::isPositiveNumber($this->shortcode['size'])) {
            return true;
        }

        return $this->isInListOfValidValues('size');
    }

    public function validateFormat() {
        return $this->isInListOfValidValues('format');
    }

    public function validateCaption() {
        return $this->isInListOfValidValues('caption');
    }

    public function validateLimit() {
        if ($this->shortcode['limit'] && !ToppaFunctions::isPositiveNumber($this->shortcode['limit'])) {
            throw new Exception($this->shortcode['limit'] . " " . __("is not a valid limit"));
        }

        return true;
    }

    public function validateOrder() {
        return $this->isInListOfValidValues('order');
    }

    public function validateReverse() {
        return $this->isInListOfValidValues('reverse');
    }

    public function validatePosition() {
        return $this->isInListOfValidValues('position');
    }

    public function validateClear() {
        return $this->isInListOfValidValues('clear');
    }

    public function validateThumbnails() {
        return $this->isAStringOfNumbersOrNull($this->shortcode['thumbnails']);
    }

    private function isInListOfValidValues($shortcodeKey) {
        if (!in_array($this->shortcode[$shortcodeKey], $this->validInputValues[$shortcodeKey])) {
            throw new Exception($this->shortcode[$shortcodeKey]. " " . __("is not a valid ") . $shortcodeKey . __(" value"));
        }

        return true;
    }

    private function isAStringOfNumbersOrNull($stringOfNumbers = null) {
        // we want comma separated numbers or a null value
        if (preg_match("/^\d+(,\d+)*$/", $stringOfNumbers) || !$stringOfNumbers) {
        }

        else {
            throw new Exception($stringOfNumbers . " " . __("is not a valid string of numbers"));
        }

        return true;
    }
}
