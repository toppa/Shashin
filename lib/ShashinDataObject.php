<?php

abstract class Lib_ShashinDataObject {
    protected $dbFacade;
    protected $tableName;
    protected $baseTableName;
    protected $data = array();
    protected $refData;
    protected $videoFileTypes = array('mpg', 'mod', 'mmv', 'tod', 'wmv', 'asf', 'avi', 'divx', 'mov', 'm4v', '3gp', '3g2', 'mp4', 'm2t', 'm2ts', 'mts', 'mkv', 'flv');

    public function __construct(Lib_ShashinDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
    }

    public function getRefData() {
        return $this->refData;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getBaseTableName() {
        return $this->baseTableName;
    }

    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        elseif (array_key_exists($name, $this->refData)) {
            return null;
        }

        throw New Exception(__("Invalid data property __get for ", "shashin") . htmlentities($name));
    }

    public function __set($name, $value) {
        if (array_key_exists($name, $this->refData)) {
            $this->data[$name] = $value;
            return true;
        }

        throw New Exception(__("Invalid data property __set for ", "shashin") . htmlentities($name));
    }

    public function getData() {
        return $this->data;
    }

    public function set(array $fields) {
        if (!is_array($fields) || empty($fields)) {
            throw New Exception(__('No array passed for set()', 'shashin'));
        }

        $intTypes = $this->dbFacade->getIntTypes();

        foreach ($this->refData as $k=>$v) {
            // needed for compatibility with mySql on Windows
            if (in_array($v['db']['type'], $intTypes) && array_key_exists($k, $fields)) {
                $fields[$k] = intval($fields[$k]);
            }
        }

        $this->data = array_merge($this->data, $fields);
        return true;
    }

    abstract public function isVideo();
    abstract public function get($key = null);
    abstract public function refresh($key);
    abstract public function flush();
    abstract public function delete();
}
