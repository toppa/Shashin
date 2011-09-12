<?php

class Admin_ShashinSettingsMenuManager {
    private $functionsFacade;
    private $settings;
    private $relativePathToTemplate = 'Display/menuSettings.php';

    public function __construct() {
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function setSettings(Lib_ShashinSettings $settings) {
        $this->settings = $settings;
    }

    public function initSettings() {
        register_setting('shashin3alpha', 'shashin3alpha', array($this, 'validateSettings'));
        $this->initGeneralSection();
        //$this->initAlbumPhotosSection();
        //$this->initHighslideSettings();
        //$this->initOtherViewerSettings();
    }

    public function validateSettings(array $userSettingInputs) {
        $validInputs = array();
        return $validInputs;
    }

    public function initGeneralSection() {
        add_settings_section(
            'shashinGeneralSettings',
            __('General Settings', 'shashin'),
            array($this, 'displaySectionHeaderGeneral'),
            'shashin3alpha'
        );
        add_settings_field(
            'shashinScheduledUpdate',
            __('Sync all albums every 10 hours', 'shashin'),
            array($this, 'displayFieldScheduledUpdate'),
            'shashin3alpha',
            'shashinGeneralSettings'
        );
    }

    public function displaySectionHeaderGeneral() {
        echo '';
    }

    public function displayFieldScheduledUpdate() {
        $refData = array('input' => array(
            'type' => 'radio',
            'subgroup' => array('y' => 'Yes', 'n' => 'No'),
        ));
        echo ToppaHtmlFormField::quickBuild('scheduledUpdate', $refData, $this->settings->scheduledUpdate);
    }

    public function displayMenu() {
        ob_start();
        require_once($this->relativePathToTemplate);
        $settingsMenu = ob_get_contents();
        ob_end_clean();
        return $settingsMenu;
    }
}