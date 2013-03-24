<?php

class Public_ShashinSessionManager {
    public function __construct() {

    }

    public function getGroupCounter() {
        if (isset($_SESSION['shashinGroupCounter'])) {
            return $_SESSION['shashinGroupCounter'];
        }

        return null;
    }

    public function setGroupCounter($value) {
        if (is_numeric($value)) {
            $_SESSION['shashinGroupCounter'] = $value;
        }

        return $_SESSION['shashinGroupCounter'];
    }

    public function getThumbnailCounter() {
        if (isset($_SESSION['shashinThumbnailCounter'])) {
            return $_SESSION['shashinThumbnailCounter'];
        }

        return null;
    }

    public function setThumbnailCounter($value) {
        if (is_numeric($value)) {
            $_SESSION['shashinThumbnailCounter'] = $value;
        }

        return $_SESSION['shashinThumbnailCounter'];
    }
}
