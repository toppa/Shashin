<?php

class Lib_ShashinAlbum extends Lib_ShashinDataObject {
    private $clonablePhoto;

    public function __construct(
      Lib_ShashinDatabaseFacade $dbFacade,
      Lib_ShashinPhoto $clonablePhoto) {

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
                'picasa' => array('gphoto$id', '$t'),
                'twitpic' => array('twitter_id'),
                'youtube' => array('id', '$t'),
                'flickr' => array('link')),
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
                'youtube' => array('id', '$t'),
                'input' => array(
                    'type' => 'text',
                    'size' => 100)),
            'user' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('gphoto$user', '$t'),
                'twitpic' => array('username'),
                'youtube' => array('author', 0, 'name', '$t')),
            'name' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('gphoto$nickname', '$t'),
                'twitpic' => array('name'),
                'youtube' => array('author', 0, 'name', '$t')),
            'linkUrl' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('link', 1, 'href'),
                'youtube' => array('link', 1, 'href'),
                'flickr' => array('link', 1, 'href')),
            'title' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255',
                    'not_null' => true),
                'picasa' => array('title', '$t'),
                'youtube' => array('title', '$t'),
                'flickr' => array('title')),
            'description' => array(
                'db' => array(
                    'type' => 'text'),
                'picasa' => array('subtitle', '$t'),
                'flickr' => array('description')),
            'location' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255'),
                'picasa' => array('gphoto$location', '$t'),
                'twitpic' => array('location')),
            'coverPhotoUrl' => array(
                'db' => array(
                    'type' => 'varchar',
                    'length' => '255'),
                'picasa' => array('icon', '$t'),
                'twitpic' => array('avatar_url'),
                'youtube' => array('logo', '$t')),
            'width' => array(
                'db' => array(
                    'type' => 'smallint unsigned')),
            'height' => array(
                'db' => array(
                    'type' => 'smallint unsigned')),
            'lastSync' => array(
                'db' => array(
                    'type' => 'int unsigned')),
            'photoCount' => array(
                'db' => array(
                    'type' => 'smallint unsigned',
                    'not_null' => true),
                'picasa' => array('gphoto$numphotos', '$t'),
                'twitpic' => array('photo_count'),
                'youtube' => array('openSearch$totalResults', '$t')),
            'pubDate' => array(
                'db' => array(
                    'type' => 'int unsigned',
                    'not_null' => true),
                'picasa' => array('gphoto$timestamp', '$t'),
                'twitpic' => array('timestamp'),
                'youtube' => array('updated', '$t')),
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

        $this->clonablePhoto = $clonablePhoto;
        $this->baseTableName = 'shashin_album';
        $this->tableName = $dbFacade->getTableNamePrefix() . $this->baseTableName;

        parent::__construct($dbFacade);
    }

    public function get($id = null) {
        // check a field we would have only if we have a fully constructed album
        if (!isset($this->data['sourceId'])) {
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