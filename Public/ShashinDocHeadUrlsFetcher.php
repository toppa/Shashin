<?php


class Public_ShashinDocHeadUrlsFetcher {
    private $functionsFacade;

    public function __construct(ToppaFunctionsFacade &$functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function getShashinCssUrl() {
        return $this->functionsFacade->getUrlforCustomizableFile('shashin.css', __FILE__, 'Display/');
    }

    public function getHighslideCssUrl() {
        return $this->functionsFacade->getUrlforCustomizableFile('highslide.css', __FILE__, 'Display/highslide/');
    }
}
