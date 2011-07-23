<?php

class Admin_ShashinMenuDisplayerPhotos extends Admin_ShashinMenuDisplayer {
    protected $album;
    protected $container;

    public function __construct(
      ToppaFunctionsFacade $functionsFacade,
      array $requests,
      Lib_ShashinPhotoCollection $collection,
      Lib_ShashinAlbum $album,
      Admin_ShashinContainer $container) {
        parent::__construct($functionsFacade, $requests, $collection);
        $this->defaultOrderBy = 'source';
        $this->relativePathToTemplate = 'Display/menuPhotos.php';
        $this->album = $album;
        $this->container = $container;
        $this->setShortcodeMimic(
            $this->requests['shashinOrderBy'],
            $this->requests['shashinReverse'],
            $this->album->id);
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortArrowAndOrderByUrl($column);
        $orderByUrl .= "&amp;shashinMenu=photos&amp;id=" . $this->album->id;
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }
}