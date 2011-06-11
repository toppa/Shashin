<?php


class Admin_ShashinDocHeadUrlsFetcher {
    private $functionsFacade;

    public function __construct(ToppaFunctionsFacade &$functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function getCssUrl() {
        return $this->functionsFacade->getUrlforCustomizableFile('admin.css', __FILE__, 'Display/');
    }

    public function getJsUrl() {
        return $this->functionsFacade->getUrlforCustomizableFile('admin.js', __FILE__, 'Display/');
    }

    public function getMenuDisplayUrl() {
        return $this->functionsFacade->getPluginsUrl('Display/', __FILE__);
    }
}
