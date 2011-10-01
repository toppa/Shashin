<?php


class Admin_ShashinHeadTags {
    private $functionsFacade;

    public function __construct(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

}
