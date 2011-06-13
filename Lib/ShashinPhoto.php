<?php

class Lib_ShashinPhoto extends Lib_ShashinDataObject {
    public function __construct(ToppaDatabaseFacade &$dbFacade) {
        $this->tableName = $dbFacade->getTableNamePrefix() . 'shashin_photo_3alpha';
        $this->refData = array(
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
            'albumKey' => array(
                'db' => array(
                    'type' => 'smallint unsigned',
                    'not_null' => true)),
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
                    'length' => '100'),
                'picasa' => array('exif$tags', 'exif$make', '$t')),
            'model' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '100'),
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

        parent::__construct($dbFacade);
    }

    public function get($photoKey = null) {
        // check a field we would have only if we have a fully constructed photo
        if (!$this->data['photoId']) {
            return $this->refresh($photoKey);
        }

        return $this->data;
    }

    public function refresh($photoKey) {
        if (!is_numeric($photoKey)) {
            throw New Exception(__("Invalid photo key", "shashin"));
        }

        $where = array("photoKey" => $photoKey);
        $this->data = $this->dbFacade->sqlSelectRow($this->tableName, null, $where);

        if (empty($this->data)) {
            throw New Exception(__("Failed to find database record for photo", "shashin"));
        }

        return $this->data;
    }

    public function delete() {
        $this->dbFacade->sqlDelete($this->tableName, array('photoKey' => $this->data['photoKey']));
        $photoData = $this->data;
        unset($this->data);
        return $photoData;
    }

    public function flush() {
        $insertId = $this->dbFacade->sqlInsert($this->tableName, $this->data, true);

        if (!$this->photoKey) {
            $this->photoKey = $insertId;
        }

        return true;
    }
}
