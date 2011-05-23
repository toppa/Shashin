<?php

abstract class ShashinDataObject {
    protected $dbFacade;
    protected $tableName;
    protected $data = array();
    protected $refData = array();

    public function __construct(&$dbFacade) {
        $this->dbFacade = $dbFacade;
    }

    public function getRefData() {
        return $this->refData;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->refData)) {
            return $this->data[$name];
        }

        throw New Exception(__("Invalid data property __get", "shashin"));
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->getRefData())) {
            $this->data[$name] = $value;
            return true;
        }

        throw New Exception(__("Invalid data property __set", "shashin"));
    }

    abstract public function get($key = null);
    abstract public function refresh($key);

    public function getData() {
        return $this->data;
    }

    public function set($fields) {
        $this->data = array_merge($this->data, $fields);
        return true;
    }

    public function flush() {
        $this->dbFacade->sqlInsert($this->tableName, $this->data, true);
        return true;
    }

    abstract public function delete();
}
