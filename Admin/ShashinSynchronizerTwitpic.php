<?php

class Admin_ShashinSynchronizerTwitpic extends Admin_ShashinSynchronizer {
    public function __construct() {
        parent::__construct();
    }

    public function syncUserRequest() {
        $this->setJsonUrlFromUserUrl();
        $album = $this->syncAlbum();
        return __('Added Twitpic photos', 'shashin') . ' "' . $album->title . '"';
    }

    public function setJsonUrlFromUserUrl() {
        // example:
        // http://twitpic.com/photos/mtoppa
        // becomes http://api.twitpic.com/2/users/show.json?username=mtoppa
        if (preg_match('#twitpic\.com/photos/(\w+)#', $this->request['userUrl'], $matches) == 1) {
            $jsonUrl = 'http://api.twitpic.com/2/users/show.json?username=' . $matches[1];
        }

        else {
            throw New Exception (__('Unrecognized URL: ', 'shashin') . htmlentities($this->request['userUrl']));
        }

        return $this->setJsonUrl($jsonUrl);
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData, $albumRefData, 'twitpic');
        $albumData['dataUrl'] = 'http://api.twitpic.com/2/users/show.json?username=' . $albumData['user'];
        $albumData['linkUrl'] = 'http://twitpic.com/photos/' . $albumData['user'];
        $albumData['title'] = 'Twitpic: ' . $albumData['name'];
        $albumData['pubDate'] = strtotime($albumData['pubDate']);
        $albumData['lastSync'] = $this->syncTime;
        $albumData['albumType'] = $this->album->albumType;
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
        if (is_array($decodedAlbumData['images'])) {
            foreach ($decodedAlbumData['images'] as $entry) {
                $photoData = $this->extractFieldsFromDecodedData($entry, $photoRefData, 'twitpic');
                $photoData['albumId'] = $this->album->id;
                $photoData['albumType'] = $this->album->albumType;
                $photoData['filename'] = $photoData['sourceId'] . '.' . $photoData['contentType'];
                $photoData['linkUrl'] = 'http://twitpic.com/' . $photoData['sourceId'];
                $photoData['contentUrl'] = 'http://twitpic.com/show/mini/' . $photoData['sourceId'];
                $photoData['takenTimestamp'] = strtotime($photoData['takenTimestamp']);
                $photoData['uploadedTimestamp'] = strtotime($photoData['uploadedTimestamp']);
                $photoData['sourceOrder'] = ++$sourceOrder;
                $photoData['lastSync'] = $this->syncTime;
                $photo = clone $this->clonablePhoto;
                $photo->set($photoData);
                $photo->flush();
            }
        }

        // twitpic pages photos, with a max of 20 per page
        if ($this->album->photoCount > 20 && $sourceOrder < $this->album->photoCount) {
            $page = ceil(($sourceOrder / 20) + 1); //adding one pushes it to the next page
            $response = $this->httpRequester->request($this->jsonUrl . "&page=$page");
            $decodedAlbumData = $this->checkResponseAndDecodeAlbumData($response);
            $this->syncAlbumPhotos($decodedAlbumData, $sourceOrder);
        }

        return $sourceOrder;
    }

    // degenerate
    public function getHighestResolutionVideoIfNeeded(array $entry, array $photoRefData) {
        return null;
    }
}
