<?php
/**
 * Created by JetBrains PhpStorm.
 * User: toppa
 * Date: 4/22/11
 * Time: 9:17 AM
 * To change this template use File | Settings | File Templates.
 */

class ShashinAlbumSet {
    private $dbFacade;
    private $clonableAlbum;
    private $tableName;
    private $albumSet = array();

    public function __construct(&$dbFacade, &$clonableAlbum) {
        $this->dbFacade = $dbFacade;
        $this->clonableAlbum = $clonableAlbum;
        $this->tableName = $this->clonableAlbum->getTableName();
    }

    public function getAllAlbums($orderByClause = null) {
        $albumData = $this->dbFacade->sqlSelectMultipleRows($this->tableName, null, null, $orderByClause);

        foreach ($albumData as $data) {
            $album = clone $this->clonableAlbum;
            $album->getAlbumFromFields($data);
            $this->albumSet[$album->albumKey] = $album;
        }

        return $this->albumSet;
    }

}
