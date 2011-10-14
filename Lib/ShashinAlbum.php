<?php

class Lib_ShashinAlbum extends Lib_ShashinDataObject {
    private $clonablePhoto;

    public function __construct(
      ToppaDatabaseFacade $dbFacade,
      Lib_ShashinAlbumRefData $refData,
      Lib_ShashinPhoto $clonablePhoto) {

        $this->clonablePhoto = $clonablePhoto;
        $this->tableName = $dbFacade->getTableNamePrefix() . 'shashin_album';

        parent::__construct($dbFacade, $refData);
    }

    public function get($id = null) {
        // check a field we would have only if we have a fully constructed album
        if (!$this->data['sourceId']) {
            return $this->refresh($id);
        }

        return $this->data;
    }

    public function refresh($id) {
        if (!is_numeric($id)) {
            throw New Exception(__("Invalid album key", "shashin"));
        }

        $where = array("id" => $id);
        $this->data = $this->dbFacade->sqlSelectRow($this->tableName, null, $where);

        if (empty($this->data)) {
            throw New Exception(__("Failed to find database record for album", "shashin"));
        }

        return $this->data;
    }

    public function delete() {
        $photosTableName = $this->clonablePhoto->getTableName();
        $this->dbFacade->sqlDelete($photosTableName, array('albumId' => $this->data['id']));
        $this->dbFacade->sqlDelete($this->tableName, array('id' => $this->data['id']));
        $albumData = $this->data;
        $this->data = array(); // do not use unset
        return $albumData;
    }

    public function flush() {
        $insertId = $this->dbFacade->sqlInsert($this->tableName, $this->data, true);

        if (!$this->id) {
            $this->id = $insertId;
        }

        return true;
    }

    // degenerate
    public function isVideo() {
        return false;
    }
}