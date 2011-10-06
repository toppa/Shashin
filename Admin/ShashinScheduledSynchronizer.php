<?php


class Admin_ShashinScheduledSynchronizer {
    private $publicContainer;
    private $clonableAlbumCollection;
    private $albumHandler;
    private $arrayShortcode = array('type' => 'album', 'order' => 'sync', 'limit' => null);
    private $albumsToSync;

    public function __construct() {
    }

    public function setPublicContainer(Public_ShashinContainer $publicContainer = null) {
        $this->publicContainer = $publicContainer;
        return $this->publicContainer;
    }

    public function setClonableAlbumCollection(Lib_ShashinAlbumCollection $clonableAlbumCollection) {
        $this->clonableAlbumCollection = $clonableAlbumCollection;
        return $this->clonableAlbumCollection;
    }

    public function setAlbumHandler(Admin_ShashinMenuActionHandlerAlbums $albumHandler) {
        $this->albumHandler = $albumHandler;
        return $this->albumHandler;
    }

    public function run() {
        $this->setArrayShortcodeLimit();
        $this->getAlbumsToSync();
        return $this->syncAlbums();
    }

    public function setArrayShortcodeLimit() {
        $shortcode = $this->publicContainer->getShortcode($this->arrayShortcode);
        $albumCollectionToCount = clone $this->clonableAlbumCollection;
        $albumCollectionToCount->setNoLimit(true);
        $totalNumberOfAlbums = $albumCollectionToCount->getCountForShortcode($shortcode);
        $this->arrayShortcode['limit'] = ceil($totalNumberOfAlbums / 24);
        return $this->arrayShortcode['limit'];
    }

    public function getAlbumsToSync() {
        $shortcode = $this->publicContainer->getShortcode($this->arrayShortcode);
        $albumCollectionToSync = clone $this->clonableAlbumCollection;
        $this->albumsToSync = $albumCollectionToSync->getCollectionForShortcode($shortcode);
        return $this->albumsToSync;
    }

    public function syncAlbums() {
        foreach ($this->albumsToSync as $album) {
            $this->albumHandler->runSynchronizerForExistingAlbum($album);
        }

        return true;
    }
}
