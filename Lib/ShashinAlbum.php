<?php

require_once('ShashinDataObject.php');

class Shashin3AlphaAlbum extends ShashinDataObject {
    private $clonablePhoto;
    private $photos = array();

    public function __construct(&$dbFacade, &$clonablePhoto) {
        $this->clonablePhoto = $clonablePhoto;
        $this->tableName = $dbFacade->getTableNamePrefix() . 'shashin_album_3alpha';
        $this->refData = array(
            'albumKey' => array(
                'db' => array(
                    'type' => 'smallint unsigned',
                    'not_null' => true,
                    'primary_key' => true,
                    'other' => 'AUTO_INCREMENT')),
            'albumId' => array(
                'db' => array(
                    'type' => 'bigint unsigned',
                    'not_null' => true),
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
                    'subgroup' => array('Y' => 'Yes', 'N' => 'No')),
            'login' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '100'),
                'input' => array(
                    'type' => 'text')),
            'password' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '100'),
                'input' => array(
                    'type' => 'password'))
            )
        );

        parent::__construct($dbFacade);
    }

    public function get($albumKey = null) {
        // check a field we would have only if we have a fully constructed album
        if (!$this->data['albumId']) {
            return $this->refresh($albumKey);
        }

        return $this->data;
    }

    public function refresh($albumKey) {
        if (!is_numeric($albumKey)) {
            throw New Exception(__("Invalid album key", "shashin"));
        }

        $where = array("albumKey" => $albumKey);
        $this->data = $this->dbFacade->sqlSelectRow($this->tableName, null, $where);

        if (empty($this->data)) {
            throw New Exception(__("Failed to find database record for album", "shashin"));
        }

        return $this->data;
    }

    public function delete() {
        $photosTableName = $this->clonablePhoto->getTableName();
        $this->dbFacade->sqlDelete($photosTableName, array('albumKey' => $this->data['albumKey']));
        $this->dbFacade->sqlDelete($this->tableName, array('albumKey' => $this->data['albumKey']));
        $albumData = $this->data;
        unset($this->data);
        return $albumData;
    }

    public function getAlbumPhotos($orderByClause = null) {
        $photosTableName = $this->clonablePhoto->getTableName();
        $where = array('albumKey' => $this->data['albumKey']);
        $photosData = $this->dbFacade->sqlSelectMultipleRows($photosTableName, null, $where, $orderByClause);

        foreach ($photosData as $data) {
            $photo = clone $this->clonablePhoto;
            $photo->setPhoto($data);
            $this->photos[$photo->photoKey] = $photo;
        }

        return $this->photos;
    }
}