<?php

class Public_ShashinOldShortcode {
    private $content;

    public function __construct($content) {
        $this->content = $content;
    }

    public function run() {
        return $this->content;
    }
}
