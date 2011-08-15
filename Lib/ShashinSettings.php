<?php

class Lib_ShashinSettings {
    private $functionsFacade;
    private $name = 'shashin3alpha';
    private $data = array();

    public function __construct(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        else {
            $this->refresh();
        }

        // and try again...
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw New Exception(__("Invalid data property __get for ", "shashin") . htmlentities($name));
    }

    public function refresh() {
        $this->data = $this->functionsFacade->getSetting($this->name);
        return $this->data;
    }

    public function set(array $newSettings) {
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
