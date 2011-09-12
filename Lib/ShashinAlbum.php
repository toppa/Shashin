<?php

class Lib_ShashinAlbum extends Lib_ShashinDataObject {
    private $clonablePhoto;
    private $photoOrderBy;
    private $photoSort;
    private $photos = array();

    public function __construct(ToppaDatabaseFacade &$dbFacade, Lib_ShashinPhoto &$clonablePhoto) {
        $this->clonablePhoto = $clonablePhoto;
        $this->tableName = $dbFacade->getTableNamePrefix() . 'shashin_album_3alpha';
        $this->refData = array(
            'id' => array(
                'db' => array(
                    'type' => 'smallint unsigned',
                    'not_null' => true,
                    'primary_key' => true,
                    'other' => 'AUTO_INCREMENT')),
            'sourceId' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true,
                    'unique_key' => true),
                'picasa' => array('gphoto$id', '$t')),
            'albumType' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '20',
                    'not_null' => true)),
            'dataUrl' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('link', 0, 'href'),
                'input' => array(
                    'type' => 'text',
                    'size' => 100)),
            'user' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('gphoto$user', '$t')),
            'name' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('gphoto$nickname', '$t')),
            'linkUrl' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('link', 1, 'href')),
            'title' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('title', '$t')),
            'description' => array(
                'db' => array(
                    'type' => 'text'),
                'picasa' => array('subtitle', '$t')),
            'location' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255'),
                'picasa' => array('gphoto$location', '$t')),
            'coverPhotoUrl' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255'),
                'picasa' => array('icon', '$t')),
            'lastSync' => array(
                'db' => array(
                    'type' => 'int unsigned')),
            'photoCount' => array(
                'db' => array(
                    'type' => 'smallint unsigned',
                    'not_null' => true),
                'picasa' => array('gphoto$numphotos', '$t')),
            'pubDate' => array(
                'db' => array(
                    'type' => 'int unsigned',
                    'not_null' => true),
                'picasa' => array('gphoto$timestamp', '$t')),
            'geoPos' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '25'),
                'picasa' => array('georss$where', 'gml$Point', 'gml$pos', '$t')),
            'includeInRandom' => array(
                'db' => array(
                    'type' => 'char',
                    'length' => '1',
                    'other' => "default 'Y'"),
                'input' => array(
                    'type' => 'radio',
                    'subgroup' => array('Y' => 'Yes', 'N' => 'No'))),
        );

        parent::__construct($dbFacade);
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

    public function isVideo() {
        return false;
    }
}