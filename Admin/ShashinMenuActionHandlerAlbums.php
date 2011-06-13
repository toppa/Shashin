<?php

class Admin_ShashinMenuActionHandlerAlbums {
    private $functionsFacade;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct(
        ToppaFunctionsFacade &$functionsFacade,
        Admin_ShashinMenuDisplayer &$menuDisplayer,
        Admin_ShashinContainer &$adminContainer,
        array &$request) {
        $this->functionsFacade = $functionsFacade;
        $this->menuDisplayer = $menuDisplayer;
        $this->adminContainer = &$adminContainer;
        $this->request = $request;
    }

    public function run() {
        try {
            switch ($this->request['shashinAction']) {
                case 'addAlbums':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceAdd', 'shashinNonceAdd');
                    $message = $this->runSynchronizerBasedOnRssUrl();
                    break;
                case 'syncAlbum':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceSync_" . $this->request['albumKey']);
                    $message = $this->runSynchronizerForExistingAlbum();
                    break;
                case 'syncAllAlbums':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceSyncAll");
                    $message = $this->runSynchronizerForAllExistingAlbums();
                    break;
                case 'deleteAlbum':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceDelete_" . $this->request['albumKey']);
                    $message = $this->runDeleteAlbum();
                    break;
                case 'updateIncludeInRandom':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceUpdate", "shashinNonceUpdate");
                    $message = $this->runUpdateIncludeInRandom();
                    break;
           }

            echo $this->menuDisplayer->run($message);
        }

        catch (Exception $e) {
            echo "<p>" . __("Shashin Error: ", "shashin") . $e->getMessage() . "</p>";
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
            $albumToSync->get($this->request['albumKey']);
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
        $albumSet = $this->adminContainer->getClonableAlbumSet();
        $albumsToSync = $albumSet->getAllAlbums();

        $albumCount = 0;
        foreach ($albumsToSync as $album) {
            $this->runSynchronizerForExistingAlbum($album);
            ++$albumCount;
        }

        return __("Synchronized all", "shashin") . " $albumCount " . __("albums", "shashin");
    }

    public function runDeleteAlbum() {
        $album = $this->adminContainer->getClonableAlbum();
        $album->get($this->request['albumKey']);
        $albumData = $album->delete();
        return __("Deleted album", "shashin") . ' "' . $albumData['title'] . '"';
    }

    public function runUpdateIncludeInRandom() {
        $albumSet = $this->adminContainer->getClonableAlbumSet();
        $albums = $albumSet->getAllAlbums();

        foreach ($this->request['includeInRandom'] as $k=>$v) {
            $albumData = array('includeInRandom' => $v);
            $albums[$k]->set($albumData);
            $albums[$k]->flush();
        }

        return __('Updated "Include In Random" settings', "shashin");
    }
 }
