<?php

class Admin_ShashinMenuDisplayerAlbums extends Admin_ShashinMenuDisplayer {
    public function __construct() {
        $this->defaultOrderBy = 'title';
        $this->relativePathToTemplate = 'display/toolsAlbums.php';
        parent::__construct();
    }

    // degenerate
    public function setAlbum(Lib_ShashinAlbum $album = null) {
        return false;
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortArrowAndOrderByUrl($column);
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }

    public function generateSyncLink(Lib_ShashinAlbum $album) {
        $url = '?page=ShashinToolsMenu&amp;shashinAction=syncAlbum&amp;id=' . $album->id;
        $nonceName = "shashinNonceSync_" . $album->id;
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = '<a href="' . $noncedUrl . '"><img src="'
            . $this->functionsFacade->getPluginsUrl('/display/images/arrow_refresh.png', __FILE__)
            . '" alt="Sync Album" width="16" height="16" border="0" /></a>';
        return $linkTag;
    }

    public function generateDeleteLink(Lib_ShashinAlbum $album) {
        $deleteWarning = __("Are you sure you want to delete this album? Any shashin tags for displaying this album will be permanently broken", "shashin");
        $onClick = "return confirm('$deleteWarning')";
        $url = '?page=ShashinToolsMenu&amp;shashinAction=deleteAlbum&amp;id=' . $album->id;
        $nonceName = "shashinNonceDelete_" . $album->id;
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = "<a href=\"$noncedUrl\" onclick=\"$onClick\"><img src=\""
            . $this->functionsFacade->getPluginsUrl('/display/images/delete.png', __FILE__)
            . '" alt="Sync Album" width="16" height="16" border="0" /></a>';
        return $linkTag;
    }

    public function generateSyncAllLink() {
        $url = '?page=ShashinToolsMenu&amp;shashinAction=syncAllAlbums';
        $nonceName = "shashinNonceSyncAll";
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = '<a href="' . $noncedUrl . '">' . __("Sync All", "shashin") . '</a>';
        return $linkTag;
    }

    public function generatePhotosMenuSwitchLink(Lib_ShashinAlbum $album) {
        $url = '?page=ShashinToolsMenu&amp;shashinMenu=photos&amp;switchingFromAlbumsMenu=1&amp;id='
            . $album->id;
        $nonceName = "shashinNoncePhotosMenu_" . $album->id;
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = '<a href="' . $noncedUrl . '">' . $album->title . '</a>';
        return $linkTag;
    }
}