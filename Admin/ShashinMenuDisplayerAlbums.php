<?php

class Admin_ShashinMenuDisplayerAlbums extends Admin_ShashinMenuDisplayer {
    private $clonableAlbum;
    private $albumCollection;

    public function __construct(
      ToppaFunctionsFacade $functionsFacade,
      array &$requests,
      Lib_ShashinAlbum $clonableAlbum,
      Lib_ShashinAlbumCollection $albumCollection) {
        $this->defaultOrderBy = 'title';
        $this->relativePathToTemplate = 'Display/menuAlbums.php';
        $this->clonableAlbum = $clonableAlbum;
        $this->albumCollection = $albumCollection;
        parent::__construct($functionsFacade, $requests);
    }

    public function run($message = null) {
        $this->checkOrderByNonce();
        $orderByClause = $this->setOrderByClause();
        $this->albumCollection->setOrderByClause($orderByClause);
        $albums = $this->albumCollection->getCollection();
        $refData = $this->clonableAlbum->getRefData();
        ob_start();
        require_once($this->relativePathToTemplate);
        $toolsMenu = ob_get_contents();
        ob_end_clean();
        return $toolsMenu;
    }

    public function generateOrderByLink($column, $columnLabel) {
        $orderByUrl = $this->setSortArrowAndOrderByUrl($column);
        $noncedUrl = $this->setOrderByNonce($column, $orderByUrl);
        return "<a href=\"$noncedUrl\">$columnLabel " . $this->sortArrow . "</a>";
    }

    public function generateSyncLink(Lib_ShashinAlbum $album) {
        $url = '?page=Shashin3AlphaToolsMenu&amp;shashinAction=syncAlbum&amp;albumKey=' . $album->albumKey;
        $nonceName = "shashinNonceSync_" . $album->albumKey;
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = '<a href="' . $noncedUrl . '"><img src="'
                . $this->functionsFacade->getPluginsUrl('/Display/images/arrow_refresh.png', __FILE__)
                . '" alt="Sync Album" width="16" height="16" border="0" /></a>';
        return $linkTag;
    }

    public function generateDeleteLink(Lib_ShashinAlbum $album) {
        $deleteWarning = __("Are you sure you want to delete this album? Any shashin tags for displaying this album will be permanently broken", "shashin");
        $onClick = "return confirm('$deleteWarning')";
        $url = '?page=Shashin3AlphaToolsMenu&amp;shashinAction=deleteAlbum&amp;albumKey=' . $album->albumKey;
        $nonceName = "shashinNonceDelete_" . $album->albumKey;
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = "<a href=\"$noncedUrl\" onclick=\"$onClick\"><img src=\""
                . $this->functionsFacade->getPluginsUrl('/Display/images/delete.png', __FILE__)
                . '" alt="Sync Album" width="16" height="16" border="0" /></a>';
        return $linkTag;
    }

    public function generateSyncAllLink() {
        $url = '?page=Shashin3AlphaToolsMenu&amp;shashinAction=syncAllAlbums';
        $nonceName = "shashinNonceSyncAll";
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = '<a href="' . $noncedUrl . '">' . __("Sync All", "shashin") . '</a>';
        return $linkTag;
    }

    public function generatePhotosMenuSwitchLink(Lib_ShashinAlbum $album) {
        $url = '?page=Shashin3AlphaToolsMenu&amp;shashinMenu=photos&amp;switchingFromAlbumsMenu=1&amp;albumKey='
            . $album->albumKey;
        $nonceName = "shashinNoncePhotosMenu_" . $album->albumKey;
        $noncedUrl = $this->functionsFacade->addNonceToUrl($url, $nonceName);
        $linkTag = '<a href="' . $noncedUrl . '">' . $album->title . '</a>';
        return $linkTag;
    }
}