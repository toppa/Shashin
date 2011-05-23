<?php

class ShashinPhotoRef {
    protected $dbFacade;
    protected $baseTableName = 'shashin_photo_3alpha';
    protected $tableName;
    protected $refData = array(
        'photoKey' => array(
            'db' => array(
                'type' => 'int unsigned',
                'not_null' => true,
                'primary_key' => true,
                'other' => 'AUTO_INCREMENT')),
        'photoId' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '255',
                'not_null' => true,
                'unique_key' => true),
            'picasa' => array('gphoto$id', '$t')),
        'albumId' => array(
            'db' => array(
                'type' => 'bigint unsigned',
                'not_null' => true),
            'picasa' => array('gphoto$albumid', '$t')),
        'title' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '255'),
            'picasa' => array('title', '$t')),
        'description' => array(
            'db' => array(
                'type' => 'text'),
            'picasa' => array('summary', '$t')),
        'linkUrl' => array(
            'db' => array(
                'type' => 'text',
                'not_null' => true),
            'picasa' => array('link', '1', 'href')),
        'contentUrl' => array(
            'db' => array(
                'type' => 'text',
                'not_null' => true),
            'picasa' => array('media$group', 'media$content', 0, 'url')),
        'contentType' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '255',
                'not_null' => true),
            'picasa' => array('media$group', 'media$content', 0, 'type')),
        'width' => array(
            'db' => array(
                'type' => 'smallint unsigned',
                'not_null' => true),
            'picasa' => array('media$group', 'media$content', 0, 'width')),
        'height' => array(
            'db' => array(
                'type' => 'smallint unsigned',
                'not_null' => true),
            'picasa' => array('media$group', 'media$content', 0, 'height')),
        'takenTimestamp' => array(
            'db' => array(
                'type' => 'int unsigned',
                'not_null' => true),
            'picasa' => array('exif$tags', 'exif$time', '$t')),
        'uploadedTimestamp' => array(
            'db' => array(
                'type' => 'int unsigned',
                'not_null' => true),
            'picasa' => array('published', '$t')),
        'tags' => array(
            'db' => array(
                'type' => 'text'),
            'picasa' => array('media$keywords', '$t')),
        'lastSync' => array(
            'db' => array(
                'type' => 'int unsigned')),
        'includeInRandom' => array(
            'db' => array(
                'type' => 'char',
                'length' => '1',
                'other' => "default 'Y'"),
            'input' => array(
                'type' => 'radio',
                'subgroup' => array('Y' => 'Yes', 'N' => 'No'))),
        'userOrder' => array(
            'db' => array(
                'type' => 'int unsigned')),
        'fstop' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '10'),
            'picasa' => array('exif$tags', 'exif$fstop', '$t')),
        'make' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '20'),
            'picasa' => array('exif$tags', 'exif$make', '$t')),
        'model' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '20'),
            'picasa' => array('exif$tags', 'exif$model', '$t')),
        'exposure' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '10'),
            'picasa' => array('exif$tags', 'exif$exposure', '$t')),
        'focalLength' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '10'),
            'picasa' => array('exif$tags', 'exif$focallength', '$t')),
        'iso' => array(
            'db' => array(
                'type' => 'varchar',
                'length' => '10'),
            'picasa' => array('exif$tags', 'exif$iso', '$t')),
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

        throw New Exception("Invalid photo data property __get", "shashin");
    }
}
