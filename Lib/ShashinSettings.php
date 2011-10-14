<?php

class Lib_ShashinSettings {
    private $functionsFacade;
    private $name = 'shashin';
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
        $oldSettings = $this->functionsFacade->getSetting($this->name);

        if (is_array($oldSettings)) {
            $this->data = $oldSettings;
        }

        return $this->data;
    }

    public function set(array $newSettings, $preferExisting = false) {
        $this->refresh();

        if ($preferExisting) {
            $this->data = array_merge($newSettings, $this->data);
        }

        else {
            $this->data = array_merge($this->data, $newSettings);
        }

        $this->functionsFacade->setSetting($this->name, $this->data);
        return $this->data;
    }

    public function delete() {
        $this->functionsFacade->deleteSetting($this->name);

        if ($this->functionsFacade->getSetting($this->name)) {
            throw new Exception(__('Failed to delete settings', 'shashin'));
        }

        return true;
    }
}
