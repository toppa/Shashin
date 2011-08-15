<?php

class Public_ShashinShortcode {
    private $rawShortcode;
    private $data = array(
        'type' => null,
        'limit' => null,
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
        'caption' => array(null, 'y', 'n', 'c'),
        'order' => array(null, 'id', 'date', 'filename', 'title', 'location', 'count', 'sync', 'random', 'source', 'user'),
        'reverse' => array(null, 'y', 'n'),
        'crop' => array(null, 'y', 'n'),
        'position' => array(null, 'left', 'right', 'none', 'inherit', 'center'),
        'clear' => array(null, 'left', 'right', 'none', 'both', 'inherit'),
    );

    public function __construct(array $rawShortcode) {
        $this->rawShortcode = $rawShortcode;
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
        return true;
    }

    public function cleanShortcode() {
        array_walk($this->rawShortcode, array('ToppaFunctions', 'trimCallback'));
        array_walk($this->rawShortcode, array('ToppaFunctions', 'strtolowerCallback'));
        return $this->rawShortcode;
    }

    public function checkValidKeysAndAssign() {
        foreach($this->rawShortcode as $k=>$v) {
            if (array_key_exists($k, $this->data)) {
                $this->data[$k] = $v;
            }

            else {
                throw New Exception(__("Invalid shortcode attribute: ", "shashin") . htmlentities($k));
            }
        }
    }

    public function checkValidValues() {
        $this->isNumericOrNull($this->data['limit']);
        $this->isAStringOfNumbersOrNull($this->data['id']);
        $this->isInListOfValidValues('caption', $this->data['caption']);
        $this->isNumericOrNull($this->data['columns']);
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

        throw new Exception(htmlentities($stringOfNumbers) . " " . __("is not a valid string of numbers"));
    }

    public function isNumericOrNull($numericString = null) {
        if (is_numeric($numericString) || !$numericString) {
            return true;
        }

        throw new Exception(htmlentities($numericString) . " " . __("is not a valid numeric value"));
    }
}
