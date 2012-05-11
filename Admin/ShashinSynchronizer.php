<?php

abstract class Admin_ShashinSynchronizer {
    protected $httpRequester;
    protected $clonableAlbum;
    protected $clonablePhoto;
    protected $dbFacade;
    protected $album;
    protected $rssUrl;
    protected $jsonUrl;
    protected $includeInRandom;
    protected $syncTime;

    public function __construct() {
    }

    public function setHttpRequester($httpRequester) {
        $this->httpRequester = $httpRequester;
    }

    public function setClonableAlbum(Lib_ShashinAlbum $clonableAlbum) {
        $this->clonableAlbum = $clonableAlbum;
    }

    public function setClonablePhoto(Lib_ShashinPhoto $clonablePhoto) {
        $this->clonablePhoto = $clonablePhoto;
    }

    public function setDatabaseFacade(ToppaDatabaseFacade $dbFacade) {
        $this->dbFacade = $dbFacade;
    }

    abstract public function deriveJsonUrl();

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
            throw new Exception(__('Invalid json url', 'shashin'));
        }

        $this->jsonUrl = $jsonUrl;
    }

    public function setIncludeInRandom($includeInRandom) {
        $this->includeInRandom = htmlentities($includeInRandom);
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

    public function syncExistingAlbum(Lib_ShashinAlbum $album) {
        $this->setJsonUrl($album->dataUrl);
        $syncedAlbum = $this->syncAlbum();
        return $syncedAlbum;
    }

    public function syncAlbum() {
        $response = $this->httpRequester->request($this->jsonUrl, array('timeout' => 30, 'sslverify' => false));
        $decodedAlbumData = $this->checkResponseAndDecodeAlbumData($response);
        $this->syncTime = time();
        return $this->syncAlbumForThisAlbumType($decodedAlbumData);
    }

    public function checkResponseAndDecodeAlbumData($response) {
        // unfortunately there's no hiding from being WP specific here
        if (is_a($response, 'WP_Error')) {
            throw new Exception(
                __('Failed to retrieve album feed at ', 'shashin')
                . $this->jsonUrl
                . '<br />'
                . __('WP_Http Error: ', 'shashin')
                . $response->get_error_message()
            );
        }

        elseif (is_array($response) && $response['response']['code'] != 200) {
            throw new Exception(
                __("Failed to retrieve album feed at ")
                . $this->jsonUrl
                . '<br />'
                . __('Error Response: ', 'shashin')
                . $response['response']['code'] . " "
                . $response['response']['message']
            );
        }

        $decodedAlbumData = json_decode($response['body'], true);

        if (!is_array($decodedAlbumData)) {
            throw new Exception(__("Failed to parse album feed at ", "shashin") . $this->jsonUrl);
        }

        return $decodedAlbumData;
    }

    abstract public function syncAlbumForThisAlbumType(array $extractedFields);
    abstract public function syncMultipleAlbumsForThisAlbumType(array $decodedMultipleAlbumsData);

    public function extractFieldsFromDecodedData(array $decodedData, array $refData, $albumType) {
        $extractedFields = array();

        foreach ($refData as $k=>$v) {
            if (!isset($v[$albumType])) {
                continue;
            }

            switch(count($v[$albumType])) {
                case 0:
                    break;
                case 1:
                    $extractedFields[$k] = $decodedData
                        [$v[$albumType][0]];
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

    abstract public function syncAlbumPhotos(array $decodedAlbumData, $sourceOrder = 0);

    public function deleteOldPhotos() {
        $sql = 'delete from ' . $this->clonablePhoto->getTableName()
            . ' where albumId = ' . $this->album->id
            . ' and lastSync < ' . ($this->syncTime - 30); // allow a buffer in case syncing is a bit slow
        return $this->dbFacade->executeQuery($sql);
    }

    abstract public function getHighestResolutionVideoIfNeeded(array $entry, array $photoRefData);
}
