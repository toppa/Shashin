<?php

class Lib_ShashinPhoto extends Lib_ShashinDataObject {
    public function __construct(ToppaDatabaseFacade $dbFacade, Lib_ShashinPhotoRefData $refData) {
        $this->tableName = $dbFacade->getTableNamePrefix() . 'shashin_photo';
        parent::__construct($dbFacade, $refData);
    }

    public function get($id = null) {
        // check a field we would have only if we have a fully constructed photo
        if (!$this->data['sourceId']) {
            return $this->refresh($id);
        }

        return $this->data;
    }

    public function refresh($id) {
        if (!is_numeric($id)) {
            throw New Exception(__("Invalid photo key", "shashin"));
        }

        $where = array("id" => $id);
        $this->data = $this->dbFacade->sqlSelectRow($this->tableName, null, $where);

        if (empty($this->data)) {
            throw New Exception(__("Failed to find database record for photo", "shashin"));
        }

        return $this->data;
    }

    public function delete() {
        $this->dbFacade->sqlDelete($this->tableName, array('id' => $this->data['id']));
        $photoData = $this->data;
        $this->data = array(); // do not use unset
        return $photoData;
    }

    public function flush() {
        $insertId = $this->dbFacade->sqlInsert($this->tableName, $this->data, true);

        if (!$this->id) {
            $this->id = $insertId;
        }

        return true;
    }

    public function isVideo() {
        $fileExtension = strtolower(ToppaFunctions::getFileExtension($this->data['filename']));

        if (in_array($fileExtension, $this->videoFileTypes)) {
            return true;
        }

        return false;
    }
}
