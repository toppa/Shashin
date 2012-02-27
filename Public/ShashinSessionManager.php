<?php

class Public_ShashinSessionManager {
    public function __construct() {

    }

    public function getGroupCounter() {
        return $_SESSION['shashinGroupCounter'];
    }

    public function setGroupCounter($value) {
        if (is_numeric($value)) {
            $_SESSION['shashinGroupCounter'] = $value;
        }

        return $_SESSION['shashinGroupCounter'];
    }

    public function getThumbnailCounter() {
        return $_SESSION['shashinThumbnailCounter'];
    }

    public function setThumbnailCounter($value) {
        if (is_numeric($value)) {
            $_SESSION['shashinThumbnailCounter'] = $value;
        }

        return $_SESSION['shashinThumbnailCounter'];
    }
}
