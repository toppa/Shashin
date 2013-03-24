<?php

class Admin_ShashinSynchronizerYoutube extends Admin_ShashinSynchronizer {
    public function __construct() {
        parent::__construct();
    }

    public function syncUserRequest() {
        $this->setJsonUrlFromUserUrl();
        $album = $this->syncAlbum();
        return __('Added YouTube videos for', 'shashin') . ' "' . $album->title . '"';
    }

    public function setJsonUrlFromUserUrl() {
        if (strpos($this->request['userUrl'], 'https://gdata.youtube.com/feeds/api') === 0) {
            $jsonUrl = $this->request['userUrl'] . '?alt=json&max-results=50';
        }

        else {
            throw New Exception (__('Unrecognized URL: ', 'shashin') . htmlentities($this->request['userUrl']));
        }

        return $this->setJsonUrl($jsonUrl);
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData['feed'], $albumRefData, 'youtube');
        // the id in the feed is often too generic, e.g. http://gdata.youtube.com/feeds/api/videos
        $albumData['sourceId'] = $this->jsonUrl;
        $albumData['dataUrl'] = $this->jsonUrl;
        // the videos are in date order, so the top one will be the most recent
        $albumData['pubDate'] = strtotime($decodedAlbumData['feed']['entry'][0]['published']['$t']);
        $albumData['lastSync'] = $this->syncTime;
        $albumData['albumType'] = $this->album->albumType;
        $albumData['width'] = 123;
        $albumData['height'] = 63;

        // 50 is the max provided in the feed
        if ($albumData['photoCount'] > 50) {
            $albumData['photoCount'] = 50;
        }

        if ($this->includeInRandom) {
            $albumData['includeInRandom'] = $this->includeInRandom;
        }

        $this->album->set($albumData);
        $this->album->flush();
        $this->syncAlbumPhotos($decodedAlbumData);
        $this->deleteOldPhotos();
        return $this->album;
    }

    // degenerate
    public function syncMultipleAlbumsForThisAlbumType(array $decodedMultipleAlbumsData) {
        return null;
    }

    public function syncAlbumPhotos(array $decodedAlbumData, $sourceOrder = 0) {
        $photoRefData = $this->clonablePhoto->getRefData();

        // don't try to process empty albums
        if (is_array($decodedAlbumData['feed']['entry'])) {
            foreach ($decodedAlbumData['feed']['entry'] as $entry) {
                $photoData = $this->extractFieldsFromDecodedData($entry, $photoRefData, 'youtube');
                $photoData['albumId'] = $this->album->id;
                $photoData['albumType'] = $this->album->albumType;
                if (isset($photoData['takenTimestamp'])) {
                    $photoData['takenTimestamp'] = strtotime($photoData['takenTimestamp']);
                }
                $photoData['uploadedTimestamp'] = strtotime($photoData['uploadedTimestamp']);
                $photoData['sourceOrder'] = ++$sourceOrder;
                $photoData['filename'] = 'youtubeWontTellTheFilename.mpg';
                $photoData['lastSync'] = $this->syncTime;
                $photo = clone $this->clonablePhoto;
                $photo->set($photoData);
                $photo->flush();
            }
        }

        return $sourceOrder;
    }

    // degenerate
    public function getHighestResolutionVideoIfNeeded(array $entry, array $photoRefData) {
        return null;
    }
}

