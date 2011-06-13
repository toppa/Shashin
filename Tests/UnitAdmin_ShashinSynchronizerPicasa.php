<?php

require_once(dirname(__FILE__) . '/../Admin/ShashinSynchronizer.php');
require_once(dirname(__FILE__) . '/../Admin/ShashinSynchronizerPicasa.php');

Mock::generate('FakeWpHttp');

class UnitAdmin_ShashinSynchronizerPicasa extends UnitTestCase {
    private $synchronizer;
    private $httpRequester;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $httpResponseBody = file_get_contents(dirname(__FILE__) . '/source_files/Picasa2007NewportRI.json');
        $httpResponse = array(
            'response' => array('code' => 200, 'message' => 'OK'),
            'body' => $httpResponseBody);
        $this->httpRequester = new MockFakeWpHttp();
        $this->httpRequester->setReturnValue('request', $httpResponse);
        $this->synchronizer = new Admin_ShashinSynchronizerPicasa($this->httpRequester);
    }

    public function testDeriveJsonUrl() {
        $rssUrl = 'http://picasaweb.google.com/data/feed/base/user/michaeltoppa/albumid/5269449390714706417?alt=rss&kind=photo&hl=en_US';
        $this->synchronizer->setRssUrl($rssUrl);
        $this->synchronizer->deriveJsonUrl();
        $expectedJsonUrl = 'http://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5269449390714706417?alt=json&kind=photo&hl=en_US';
        $jsonUrl = $this->synchronizer->getJsonUrl();
        $this->assertEqual($jsonUrl, $expectedJsonUrl);
    }

    public function testCheckResponseAndDecodeAlbumData() {
        $dummyUrl = 'http://www.nowhere.com';
        $response = $this->httpRequester->request($dummyUrl);
        $decodedAlbumData = $this->synchronizer->checkResponseAndDecodeAlbumData($response);
        $expectedDecodedAlbumData = file_get_contents(dirname(__FILE__) . '/source_files/Picasa2007NewportRI_decoded.txt');
        $expectedDecodedAlbumData = unserialize($expectedDecodedAlbumData);
        $this->assertEqual($decodedAlbumData, $expectedDecodedAlbumData);
    }

    public function testExtractFieldsFromDecodedData() {
        $refData = file_get_contents(dirname(__FILE__) . '/source_files/album_ref_data.txt');
        $refData = unserialize($refData);
        $decodedAlbumData = file_get_contents(dirname(__FILE__) . '/source_files/Picasa2007NewportRI_decoded.txt');
        $decodedAlbumData = unserialize($decodedAlbumData);
        $expectedRawAlbumData = array(
            "album_id" => 5082765283068156849,
            "data_url" => "http://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5082765283068156849?alt=json",
            "user" => "michaeltoppa",
            "name" => "Mike",
            "link_url" => "http://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5082765283068156849?alt=json&tok=B0EGD2Yf_Vmjr-Vb3B_6_lZxk8c",
            "title" => "2007 - Newport, RI",
            "description" => "Pictures from trips visiting my family in Newport",
            "location" => "Newport, RI",
            "cover_photo_url" => "http://lh4.ggpht.com/_e1IlgcNcTSg/RomcGGX3G7E/AAAAAAAAEmQ/ccUn4vvp0Yw/s160-c/2007NewportRI.jpg",
            "photo_count" => 18,
            "pub_date" => "1180249200000",
            "geo_pos" => "41.49209 -71.31133"
        );
        $albumRawData = $this->synchronizer->extractFieldsFromDecodedData($decodedAlbumData['feed'], $refData, 'picasa');
        $this->assertEqual($expectedRawAlbumData, $albumRawData);
    }
}

class FakeWpHttp {
    public function __construct() {

    }

    public function request($url) {
        return $url;
    }
}
