<?php
/**
 * Created by JetBrains PhpStorm.
 * User: toppa
 * Date: 4/11/11
 * Time: 8:11 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class ShashinSynchronizer {
    protected $httpRequester;
    protected $photoRef;
    protected $clonablePhoto;
    protected $albumRef;
    protected $clonableAlbum;
    protected $rssUrl;
    protected $jsonUrl;
    protected $includeInRandom;

    public function __construct() {
    }

    public function setHttpRequester(&$httpRequester) {
        $this->httpRequester = $httpRequester;
    }

    public function setRssUrl($rssUrl) {
        // use strip_tags instead of htmlentities, since the URL will
        // probably contain special chars that we shouldn't convert
        $this->rssUrl = trim(strip_tags($rssUrl));
    }

    public function getJsonUrl() {
        return $this->jsonUrl;
    }

    public function setJsonUrl($jsonUrl) {
        if (!is_string($jsonUrl)) {
            throw new Exception(__("Invalid json url", "shashin"));
        }

        $this->jsonUrl = $jsonUrl;
    }

    public function setIncludeInRandom($includeInRandom) {
        $this->includeInRandom = htmlentities($includeInRandom);
    }

    public function setAlbumRef(&$albumRef) {
        $this->albumRef = $albumRef;
    }

    public function setClonableAlbum(&$album) {
        $this->clonableAlbum = $album;
    }

    public function setPhotoRef(&$photoRef) {
        $this->photoRef = $photoRef;
    }

    public function setClonablePhoto(&$clonablePhoto) {
        $this->clonablePhoto = $clonablePhoto;
    }

    public function addSingleAlbumFromRssUrl() {
        $this->deriveJsonUrl();
        $album = $this->syncAlbum();
        return $album;
    }

    public function addMultipleAlbumsFromRssUrl() {
        $this->deriveJsonUrl();
        $response = $this->httpRequester->request($this->jsonUrl);
        $decodedMultipleAlbumsData = $this->checkResponseAndDecodeAlbumData($response);
        $albumCount = $this->syncMultipleAlbumsForThisAlbumType($decodedMultipleAlbumsData);
        return $albumCount;
    }

    public function syncExistingAlbum($album) {
        $this->setJsonUrl($album->dataUrl);
        $syncedAlbum = $this->syncAlbum();
        return $syncedAlbum;
    }

    public function syncAlbum() {
        $response = $this->httpRequester->request($this->jsonUrl);
        $decodedAlbumData = $this->checkResponseAndDecodeAlbumData($response);
        return $this->syncAlbumForThisAlbumType($decodedAlbumData);
    }

    public function checkResponseAndDecodeAlbumData($response) {
        // don't have an interface for WP_Http :-( this could be a WP_Error object
        if (!is_array($response)) {
            throw new Exception(__("Failed to retrieve album feed at ", "shashin") . $this->jsonUrl);
        }

        if ($response['response']['code'] != 200) {
            throw new Exception(__("Failed to retrieve album feed at ")
                . $this->jsonUrl . " "
                . $response['response']['code'] . " "
                . $response['response']['message']);
        }

        $decodedAlbumData = json_decode($response['body'], true);

        if (!is_array($decodedAlbumData)) {
            throw new Exception(__("Failed to parse album feed at ", "shashin") . $this->jsonUrl);
        }

        return $decodedAlbumData;
    }

    public function extractFieldsFromDecodedData($decodedData, $refData, $albumType) {
        $extractedFields = array();

        foreach ($refData as $k=>$v) {
            switch(count($v[$albumType])) {
                case 0:
                    break;
                case 2:
                    $extractedFields[$k] = $decodedData
                        [$v[$albumType][0]]
                        [$v[$albumType][1]];
                    break;
                case 3:
                    $extractedFields[$k] = $decodedData
                        [$v[$albumType][0]]
                        [$v[$albumType][1]]
                        [$v[$albumType][2]];
                    break;
                case 4:
                    $extractedFields[$k] = $decodedData
                        [$v[$albumType][0]]
                        [$v[$albumType][1]]
                        [$v[$albumType][2]]
                        [$v[$albumType][3]];
                    break;
                default:
                    throw new Exception(__("Unexpected number of fields in feed", "shashin"));
            }
        }

        return $extractedFields;
    }

    abstract public function deriveJsonUrl();
    abstract public function syncAlbumForThisAlbumType($extractedFields);
    abstract public function syncAlbumPhotos($decodedAlbumData);
    abstract public function syncMultipleAlbumsForThisAlbumType($decodedMultipleAlbumsData);
}
