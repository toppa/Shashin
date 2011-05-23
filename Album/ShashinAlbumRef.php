<?php

class ShashinAlbumRef {
    private $dbFacade;
    private $baseTableName = 'shashin_album_3alpha';
    private $tableName;
    private $refData = array(
        'albumKey' => array(
            'db' => array(
                'type' => 'smallint unsigned',
                'not_null' => true,
                'primary_key' => true,
                'other' => 'AUTO_INCREMENT')),
        'albumId' => array(
            'db' => array(
                'type' => 'bigint unsigned',
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
                'subgroup' => array('Y' => 'Yes', 'N' => 'No')),
        'email' => array(
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

    public function __construct(&$dbFacade) {
        $this->dbFacade = $dbFacade;
        $this->tableName = $this->dbFacade->getTableNamePrefix() . $this->baseTableName;
    }

    public function getRefData() {
        return $this->refData;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->refData)) {
            return $this->refData[$name];
        }

        throw New Exception("Invalid album data property __get", "shashin");
    }
}