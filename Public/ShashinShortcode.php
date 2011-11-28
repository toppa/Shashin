<?php

class Public_ShashinShortcode {
    private $arrayShortcode;
    private $settings;
    private $data = array(
        'type' => null,
        'limit' => null,
        'offset' => null,
        'size' => null,
        'id' => null,
        'caption' => null,
        'columns' => null,
        'order' => null,
        'reverse' => null,
        'crop' => null,
        'thumbnail' => null,
        'position' => null,
        'clear' => null
    );

    private $validInputValues = array(
        'caption' => array(null, 'y', 'n'),
        'order' => array(null, 'id', 'date', 'filename', 'title', 'location', 'count', 'sync', 'random', 'source', 'user', 'uploaded'),
        'reverse' => array(null, 'y', 'n'),
        'crop' => array(null, 'y', 'n'),
        'position' => array(null, 'left', 'right', 'none', 'inherit', 'center'),
        'clear' => array(null, 'left', 'right', 'none', 'both', 'inherit'),
    );

    public function __construct(array $arrayShortcode) {
        $this->arrayShortcode = $arrayShortcode;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
        return $this->settings;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw New Exception(__('Invalid data property __get for: ', 'shashin') . htmlentities($name));
    }

    public function cleanAndValidate() {
        $this->cleanShortcode();
        $this->checkValidKeysAndAssign();
        $this->checkValidValues();
        $this->checkColumnsAndSizeAreNotBothMax();
        return $this->arrayShortcode;
    }

    public function cleanShortcode() {
        array_walk($this->arrayShortcode, array('ToppaFunctions', 'trimCallback'));
        array_walk($this->arrayShortcode, array('ToppaFunctions', 'strtolowerCallback'));
        return $this->arrayShortcode;
    }

    public function checkValidKeysAndAssign() {
        foreach($this->arrayShortcode as $k=>$v) {
            if (array_key_exists($k, $this->data)) {
                $this->data[$k] = $v;
            }

            else {
                throw New Exception(__('Invalid shortcode attribute: ', 'shashin') . htmlentities($k));
            }
        }
    }

    public function checkValidValues() {
        $this->isNumericOrNull($this->data['limit']);
        $this->isNumericOrNull($this->data['offset']);
        $this->isAStringOfNumbersOrNull($this->data['id']);
        $this->isInListOfValidValues('caption', $this->data['caption']);
        $this->isNumericOrNullOrMax($this->data['columns']);
        $this->isInListOfValidValues('order', $this->data['order']);
        $this->isInListOfValidValues('reverse', $this->data['reverse']);
        $this->isInListOfValidValues('crop', $this->data['crop']);
        $this->isAStringOfNumbersOrNull($this->data['thumbnail']);
        $this->isInListOfValidValues('position', $this->data['position']);
        $this->isInListOfValidValues('clear', $this->data['clear']);
        return true;
    }

    public function isInListOfValidValues($shortcodeKey, $value) {
        if (in_array($value, $this->validInputValues[$shortcodeKey])) {
            return true;
        }

        throw new Exception(htmlentities($value) . __(' is not a valid value for: ', 'shashin') . $shortcodeKey);
    }

    public function isAStringOfNumbersOrNull($stringOfNumbers = null) {
        // we want comma separated numbers or a null value
        if (preg_match("/^[\s\d,]+$/", $stringOfNumbers) || !$stringOfNumbers) {
            return true;
        }

        throw new Exception(htmlentities($stringOfNumbers) . " " . __('is not a valid string of numbers', 'shashin'));
    }

    public function isNumericOrNullOrMax($string = null) {
        if ($string == 'max') {
            return true;
        }

        return $this->isNumericOrNull($string);
    }

    public function isNumericOrNull($string = null) {
        if (is_numeric($string) || !$string) {
            return true;
        }

        throw new Exception(htmlentities($string) . " " . __('is not a valid numeric value', 'shashin'));
    }

    public function checkColumnsAndSizeAreNotBothMax() {
        if ($this->data['columns'] == 'max' && $this->data['size'] == 'max') {
            throw New Exception (__('"size" and "columns" can not both me "max"', 'shashin'));
        }

        return true;
    }
}
