<?php

class Admin_ShashinMenuDisplayerPhotos extends Admin_ShashinMenuDisplayer {
    public function __construct() {
        parent::__construct();
        $this->defaultOrderBy = 'source';
        $this->relativePathToTemplate = 'Display/menuPhotos.php';
        $this->setShortcodeMimic(
            $this->request['shashinOrderBy'],
            $this->request['shashinReverse'],
            $this->album->id);
    }

    public function setAlbum(Lib_ShashinAlbum $album = null) {
        $this->album = $album;
    }

    public function setContainer(Public_ShashinContainer $container = null) {
        $this->container = $container;
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortArrowAndOrderByUrl($column);
        $orderByUrl .= "&amp;shashinMenu=photos&amp;id=" . $this->album->id;
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }
}