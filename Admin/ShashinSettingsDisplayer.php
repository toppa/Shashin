<?php

class Admin_ShashinSettingsDisplayer {
    private $functionsFacade;
    private $relativePathToTemplate = 'Display/menuSettings.php';

    public function __construct(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function run() {
        ob_start();
        require_once($this->relativePathToTemplate);
        $settingsMenu = ob_get_contents();
        ob_end_clean();
        return $settingsMenu;
    }
}