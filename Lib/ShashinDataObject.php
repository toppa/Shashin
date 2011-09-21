<?php

abstract class Lib_ShashinDataObject {
    protected $dbFacade;
    protected $tableName;
    protected $data = array();
    protected $refData;
    protected $videoFileTypes = array('mpg', 'avi', 'asf', 'wmv', 'mov', 'mp4');

    public function __construct(ToppaDatabaseFacade $dbFacade, Lib_ShashinDataObjectRefData $refData) {
        $this->dbFacade = $dbFacade;
        $this->refData = $refData;
    }

    public function getRefData() {
        return $this->refData->getRefData();
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->getRefData())) {
            return $this->data[$name];
        }

        throw New Exception(__("Invalid data property __get for ", "shashin") . htmlentities($name));
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->getRefData())) {
            $this->data[$name] = $value;
            return true;
        }

        throw New Exception(__("Invalid data property __set for ", "shashin") . htmlentities($name));
    }

    public function getData() {
        return $this->data;
    }

    public function set(array $fields) {
        $this->data = array_merge($this->data, $fields);
        return true;
    }

    abstract public function isVideo();
    abstract public function get($key = null);
    abstract public function refresh($key);
    abstract public function flush();
    abstract public function delete();
}
