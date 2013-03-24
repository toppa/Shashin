<?php

class Admin_ShashinMenuDisplayerPhotos extends Admin_ShashinMenuDisplayer {
    public function __construct() {
        $this->defaultOrderBy = 'source';
        $this->relativePathToTemplate = 'display/toolsPhotos.php';
        parent::__construct();
    }

    public function setAlbum(Lib_ShashinAlbum $album = null) {
        $this->album = $album;
        return $this->album;
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortArrowAndOrderByUrl($column);
        $orderByUrl .= "&amp;shashinMenu=photos&amp;id=" . $this->album->id;
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }
}