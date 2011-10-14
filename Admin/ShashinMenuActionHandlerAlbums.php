<?php

class Admin_ShashinMenuActionHandlerAlbums {
    private $functionsFacade;
    private $upgrader;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct() {
    }

    public function setFunctionsFacade(ToppaFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setUpgrader(Admin_ShashinUpgradeWp $upgrader) {
        $this->upgrader = $upgrader;
        return $this->upgrader;
    }

    public function setMenuDisplayer(Admin_ShashinMenuDisplayer $menuDisplayer) {
        $this->menuDisplayer = $menuDisplayer;
        return $this->menuDisplayer;
    }

    public function setAdminContainer(Admin_ShashinContainer $adminContainer) {
        $this->adminContainer = $adminContainer;
        return $this->adminContainer;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function run() {
        try {
            switch ($this->request['shashinAction']) {
                case 'addAlbums':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceAdd', 'shashinNonceAdd');
                    $message = $this->runSynchronizerBasedOnRssUrl();
                    break;
                case 'syncAlbum':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceSync_' . $this->request['id']);
                    $message = $this->runSynchronizerForExistingAlbum();
                    break;
                case 'syncAllAlbums':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceSyncAll');
                    $message = $this->runSynchronizerForAllExistingAlbums();
                    break;
                case 'deleteAlbum':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceDelete_' . $this->request['id']);
                    $message = $this->runDeleteAlbum();
                    break;
                case 'updateIncludeInRandom':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceUpdate', 'shashinNonceUpdate');
                    $message = $this->runUpdateIncludeInRandom();
                    break;
                case 'cleanupUpgrade':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceCleanupUpgrade');
                    $this->upgrader->cleanup();
                    $message = __('Upgrade cleanup completed', 'shashin');
            }

            return $this->menuDisplayer->run($message);
        }

        catch (Exception $e) {
            return "<p>" . __("Shashin Error: ", "shashin") . $e->getMessage() . "</p>";
        }

        return true;
    }

    public function runSynchronizerBasedOnRssUrl() {
        // all of a Picasa user's albums
        if (strpos($this->request['rssUrl'], 'kind=album') !== false) {
            $synchronizer = $this->adminContainer->getSynchronizerPicasa($this->request);
            $albumCount = $synchronizer->addMultipleAlbumsFromRssUrl();
            return __("Added", "shashin") . " $albumCount " . __("albums", "shashin");
        }

        // a single Picasa album
        else if (strpos($this->request['rssUrl'], 'kind=photo') !== false) {
            $synchronizer = $this->adminContainer->getSynchronizerPicasa($this->request);
            $syncedAlbum = $synchronizer->addSingleAlbumFromRssUrl();
            return __("Synchronized album", "shashin") . ' "' . $syncedAlbum->title . '"';
        }

        else {
            throw new Exception(__("Unrecognized RSS feed", "shashin"));
        }
    }

    public function runSynchronizerForExistingAlbum(Lib_ShashinAlbum $albumToSync = null) {
        if (!$albumToSync) {
            $albumToSync = $this->adminContainer->getClonableAlbum();
            $albumToSync->get($this->request['id']);
        }

        switch ($albumToSync->albumType) {
            case 'picasa':
                $synchronizer = $this->adminContainer->getSynchronizerPicasa();
                $syncedAlbum = $synchronizer->syncExistingAlbum($albumToSync);
                return __("Synchronized album", "shashin") . ' "' . $syncedAlbum->title . '"';
                break;
            default:
                throw new Exception(__("Unrecognized album type", "shashin"));
        }
    }

    public function runSynchronizerForAllExistingAlbums() {
        $albumCollection = $this->adminContainer->getClonableAlbumCollection();
        $albumsToSync = $albumCollection->getCollection();

        $albumCount = 0;
        foreach ($albumsToSync as $album) {
            $this->runSynchronizerForExistingAlbum($album);
            ++$albumCount;
        }

        return __("Synchronized all", "shashin") . " $albumCount " . __("albums", "shashin");
    }

    public function runDeleteAlbum() {
        $album = $this->adminContainer->getClonableAlbum();
        $album->get($this->request['id']);
        $albumData = $album->delete();
        return __("Deleted album", "shashin") . ' "' . $albumData['title'] . '"';
    }

    public function runUpdateIncludeInRandom() {
        $shortcodeMimic = array('type' => 'album', 'order' => 'title');
        $albums = $this->menuDisplayer->getDataObjects($shortcodeMimic);

        foreach ($albums as $album) {
            $albumData = array('includeInRandom'=> $this->request['includeInRandom'][$album->id]);
            $album->set($albumData);
            $album->flush();
        }

        return __('Updated "Include In Random" settings', "shashin");
    }
 }
