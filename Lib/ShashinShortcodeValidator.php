<?php

class Lib_ShashinShortcodeValidator {

    public function __construct() {
    }




    public function validateFormat($format = null) {
        return $this->isInListOfValidValues('format', $format);
    }

    public function validateCaption($caption = null) {
        return $this->isInListOfValidValues('caption', $caption);
    }




    public function validatePosition($position = null) {
        return $this->isInListOfValidValues('position', $position);
    }

    public function validateClear($clear = null) {
        return $this->isInListOfValidValues('clear', $clear);
    }


}
