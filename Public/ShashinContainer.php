<?php

class Public_ShashinContainer extends Lib_ShashinContainer {
    public function __construct($autoLoader) {
        parent::__construct($autoLoader);
    }


    public function getAlbumLayoutManager() {
        $this->getSettings();
        $this->getFunctionsFacade();
        return new Public_ShashinAlbumLayoutManager($this->settings, $this->functionsFacade);
    }

    public function getPhotoLayoutManager() {
        $this->getSettings();
        $this->getFunctionsFacade();
        return new Public_ShashinPhotoLayoutManager($this->settings, $this->functionsFacade);
    }
}
