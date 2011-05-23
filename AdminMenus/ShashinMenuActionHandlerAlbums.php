<?php
/**
 * Created by JetBrains PhpStorm.
 * User: toppa
 * Date: 4/27/11
 * Time: 8:04 AM
 * To change this template use File | Settings | File Templates.
 */

class ShashinMenuActionHandlerAlbums {
    private $functionsFacade;
    private $menuDisplayer;
    private $objectsSetup;
    private $requests;

    public function __construct(&$functionsFacade, &$menuDisplayer, &$objectsSetup, &$requests) {
        $this->functionsFacade = $functionsFacade;
        $this->menuDisplayer = $menuDisplayer;
        $this->objectsSetup = $objectsSetup;
        $this->requests = $requests;
    }

    public function run() {
        try {
            switch ($this->requests['shashinAction']) {
                case 'addAlbums':
                    $this->functionsFacade->checkAdminNonceFields('shashinNonceAdd', 'shashinNonceAdd');
                    $message = $this->runSynchronizerBasedOnRssUrl();
                    break;
                case 'syncAlbum':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceSync_" . $this->requests['albumKey']);
                    $message = $this->runSynchronizerForExistingAlbum();
                    break;
                case 'syncAllAlbums':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceSyncAll");
                    $message = $this->runSynchronizerForAllExistingAlbums();
                    break;
                case 'deleteAlbum':
                    $this->functionsFacade->checkAdminNonceFields("shashinNonceDelete_" . $this->requests['albumKey']);
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
        if (strpos($this->requests['rssUrl'], 'kind=album') !== false) {
            $synchronizer = $this->objectsSetup->setupSynchronizerPicasa($this->requests);
            $albumCount = $synchronizer->addMultipleAlbumsFromRssUrl();
            return __("Added", "shashin") . " $albumCount " . __("albums", "shashin");
        }

        // a single Picasa album
        else if (strpos($this->requests['rssUrl'], 'kind=photo') !== false) {
            $synchronizer = $this->objectsSetup->setupSynchronizerPicasa($this->requests);
            $syncedAlbum = $synchronizer->addSingleAlbumFromRssUrl();
            return __("Synchronized album", "shashin") . ' "' . $syncedAlbum->title . '"';
        }

        else {
            throw new Exception(__("Unrecognized RSS feed", "shashin"));
        }
    }

    public function runSynchronizerForExistingAlbum($albumToSync = null) {
        if (!$albumToSync) {
            $albumToSync = $this->objectsSetup->setupAlbum($this->requests['albumKey']);
        }

        switch ($albumToSync->albumType) {
            case 'picasa':
                $synchronizer = $this->objectsSetup->setupSynchronizerPicasa();
                $syncedAlbum = $synchronizer->syncExistingAlbum($albumToSync);
                return __("Synchronized album", "shashin") . ' "' . $syncedAlbum->title . '"';
                break;
            default:
                throw new Exception(__("Unrecognized album type", "shashin"));
        }
    }

    public function runSynchronizerForAllExistingAlbums() {
        $albumSet = $this->objectsSetup->setupAlbumSet();
        $albumsToSync = $albumSet->getAllAlbums();

        $albumCount = 0;
        foreach ($albumsToSync as $album) {
            $this->runSynchronizerForExistingAlbum($album);
            ++$albumCount;
        }

        return __("Synchronized all", "shashin") . " $albumCount " . __("albums", "shashin");
    }

    public function runDeleteAlbum() {
        $album = $this->objectsSetup->setupAlbum($this->requests['albumKey']);
        $albumTitle = $album->title;
        $album->deleteAlbum();
        return __("Deleted album", "shashin") . ' "' . $albumTitle . '"';
    }

    public function runUpdateIncludeInRandom() {
        $albumSet = $this->objectsSetup->setupAlbumSet();
        $albums = $albumSet->getAllAlbums();

        foreach ($this->requests['includeInRandom'] as $k=>$v) {
            $fields = array('includeInRandom'=> $v);
            $albums[$k]->setAlbum($fields);
        }

        return __('Updated "Include In Random" settings', "shashin");
    }
 }
