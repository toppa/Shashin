<?php

class Lib_ShashinSettings {
    private $functionsFacade;
    private $name = 'shashin3alpha';
    private $data = array();

    public function __construct(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function get() {
        if (empty($this->data)) {
            return $this->refresh();
        }

        return $this->data;
    }

    public function refresh() {
        $this->data = $this->functionsFacade->getSetting($this->name);
        return $this->data;
    }

    public function set($newSettings) {
        if (!is_array($newSettings)) {
            throw new Exception(__('Invalid settings', 'shashin'));
        }

        $oldSettings = $this->refresh();

        if (is_array($oldSettings)) {
            $this->data = array_merge($oldSettings, $newSettings);
        }

        else {
            $this->data = $newSettings;
        }

        if ($this->data != $oldSettings) {
            if (!$this->functionsFacade->setSetting($this->name, $this->data)) {
                throw new Exception(__('Failed to update settings', 'shashin'));
            }
        }

        return true;
    }

    public function delete() {
        $this->functionsFacade->deleteSetting($this->name);

        if ($this->functionsFacade->getSetting($this->name)) {
            throw new Exception(__('Failed to delete settings', 'shashin'));
        }

        return true;
    }
}
