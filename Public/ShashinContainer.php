<?php

class Public_ShashinContainer extends Lib_ShashinContainer {
    public function __construct($autoLoader) {
        parent::__construct($autoLoader);
    }

    public function getLayoutManager() {
        $this->getSettings();

        return new Public_ShashinLayoutManager($this->settings);
    }
}
