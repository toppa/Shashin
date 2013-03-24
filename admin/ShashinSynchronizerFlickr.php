<?php

class Admin_ShashinSynchronizerFlickr extends Admin_ShashinSynchronizer {
    public function __construct() {
        parent::__construct();
    }

    public function deriveJsonUrl() {
        // example:
        // http://api.flickr.com/services/feeds/photoset.gne?set=72157624624016813&nsid=60624157@N00&lang=en-us
        // http://api.flickr.com/services/feeds/photoset.gne?set=72157624624016813&nsid=60624157@N00&lang=en-us&format=json
        // http://api.flickr.com/services/rest/?method=flickr.photosets.getInfo&api_key=fa807a54210159cb56aaa496069efb49&photoset_id=72157624624016813&format=json
        $jsonUrl = $this->rssUrl . '&format=json';
        $this->setJsonUrlFromUserUrl($jsonUrl);
    }

    public function syncAlbumForThisAlbumType(array $decodedAlbumData) {
        $this->album = clone $this->clonableAlbum;
        $albumRefData = $this->clonableAlbum->getRefData();
        $albumData = $this->extractFieldsFromDecodedData($decodedAlbumData, $albumRefData, 'flickr');
        $linkUrlParts = explode('/', $albumData['sourceId']);
        $albumData['sourceId'] = $linkUrlParts[count($linkUrlParts - 1)];
        $albumData['dataUrl'] = $this->jsonUrl;
        $albumData['user'] = $albumData['items'][0]['author_id'];
        $authorParts = explode(' (', $albumData['items'][0]['author']);
        $albumData['name'] = substr($authorParts[1], 0, -1);



        $albumData['pubDate'] = strtotime($albumData['pubDate']);
        $albumData['lastSync'] = $this->syncTime;
        $albumData['albumType'] = 'twitpic';
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
