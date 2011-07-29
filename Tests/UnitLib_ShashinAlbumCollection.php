<?php
// these tests also cover methods in the parent ShashinDataObjectCollection class
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaDatabaseFacadeWp.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumCollection.php');
Mock::generate('ToppaDatabaseFacadeWp');
Mock::generate('Lib_ShashinAlbum');


class UnitLib_ShashinAlbumCollection extends UnitTestCase {
    private $dbFacade;
    private $clonableAlbum;
    private $albumData = array(
      array(
        "id" => "1",
        "sourceId" => "5106043314877888401",
        "albumType" => "picasa",
        "dataUrl" => "https://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5106043314877888401?alt=json",
        "user" => "michaeltoppa",
        "name" => "Michael Toppa",
        "linkUrl" => "https://picasaweb.google.com/michaeltoppa/1999MikeAndMariaSWedding",
        "title" => "1999 - Mike and Maria's Wedding",
        "description" => "",
        "location" => "Stanford, CA",
        "coverPhotoUrl" => "https://lh4.googleusercontent.com/-b7rVxWSjQak/RtxPV4cN95E/AAAAAAAAIg8/IivNZh6uXV8/s160-c/1999MikeAndMariaSWedding.jpg",
        "lastSync" => "1310827261",
        "photoCount" => "41",
        "pubDate" => "936428400",
        "geoPos" => "37.424106 -122.166076",
        "includeInRandom" => "Y",
        "login" => NULL,
        "password" => NULL
      ),
      array(
        "id" => "2",
        "sourceId" => "5100917533403116161",
        "albumType" => "picasa",
        "dataUrl" => "https://picasaweb.google.com/data/feed/api/user/michaeltoppa/albumid/5100917533403116161?alt=json",
        "user" => "michaeltoppa",
        "name" => "Michael Toppa",
        "linkUrl" => "https://picasaweb.google.com/michaeltoppa/Various",
        "title" => "Various",
        "description" => "",
        "location" => "",
        "coverPhotoUrl" => "https://lh3.googleusercontent.com/-oFx4v81evEE/RsoZeIcN8oE/AAAAAAAAIrY/NUcgBJBy7RE/s160-c/Various.jpg",
        "lastSync" => "1310827343",
        "photoCount" => "59",
        "pubDate" => "1187649754",
        "geoPos" => "",
        "includeInRandom" => "Y",
        "login" => NULL,
        "password" => NULL
      )
    );

    private $validTestCases = array(
        'photoIdXsmallThumbs' => array('type' => 'photo', 'id' => '1,2,3', 'size' => 'x-small', 'thumbnail' => '4,5,6'),
        'photoIdMediumCenter' => array('type' => 'photo', 'id' => '1,2,3', 'size' => 'medium', 'position' => 'center'),
        'albumIdUserClear' => array('type' => 'album', 'id' => '1,3,2', 'order' => 'user', 'clear' => 'both'),
        'photo160RandomListCaption' => array('type' => 'photo', 'size' => '160', 'order' => 'random', 'format' => 'list', 'caption' => 'y'),
        'photoIdDateReverseTableLimit' => array('type' => 'photo', 'id' => '1,2,3', 'order' => 'date', 'reverse' => 'y', 'format' => 'table', 'limit' => 3),
    );

    private $invalidTestCases = array(
        'invalid' => array(
            'type' => 'ahoy',
            'id' => '1,2,b,3',
            'size' => 'n',
            'format' => 'nonsense',
            'caption' => 'x',
            'limit' => 'z',
            'order' => 'y',
            'reverse' => '42',
            'position' => 'a',
            'clear' => 'b',
            'thumbnail' => '1,2,b,3'
        ),
    );

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->dbFacade = new MockToppaDatabaseFacadeWp();
        $this->clonableAlbum = new MockLib_ShashinAlbum();
        $this->clonableAlbum->setReturnValue('getTableName', 'wp_shashin_album_3alpha');
    }

    public function testSetIdString() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        try {
            $albumCollection->setIdString($this->invalidTestCases['invalid']['id']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }

        foreach ($this->validTestCases as $case) {
            $this->assertEqual($albumCollection->setIdString($case['id']), $case['id']);
        }
    }

    public function testSetLimitClause() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        try {
            $albumCollection->setLimitClause($this->invalidTestCases['invalid']['limit']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }

        $shouldHaveNoLimit = $albumCollection->setLimitClause($this->validTestCases['photoIdXsmallThumbs']['limit']);
        $this->assertEqual($shouldHaveNoLimit, null);
        $shouldHaveLimit3 = $albumCollection->setLimitClause($this->validTestCases['photoIdDateReverseTableLimit']['limit']);
        $this->assertEqual($shouldHaveLimit3, 'limit 3');
    }

    public function testSetOrderBy() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        try {
            $albumCollection->setOrderBy($this->invalidTestCases['invalid']['order']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }

        $shouldDefaultToPubDate = $albumCollection->setOrderBy($this->validTestCases['photoIdXsmallThumbs']['order']);
        $this->assertEqual($shouldDefaultToPubDate, 'pubDate');
        $shouldBeUser = $albumCollection->setOrderBy($this->validTestCases['albumIdUserClear']['order']);
        $this->assertEqual($shouldBeUser, 'user');
        $shouldBeRandom = $albumCollection->setOrderBy($this->validTestCases['photo160RandomListCaption']['order']);
        $this->assertEqual($shouldBeRandom, 'rand()');
        $shouldBePubDate = $albumCollection->setOrderBy($this->validTestCases['photoIdDateReverseTableLimit']['order']);
        $this->assertEqual($shouldBePubDate, 'pubDate');
    }


    public function testSetSort() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        try {
            $albumCollection->setSort($this->invalidTestCases['invalid']['reverse']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }

        $shouldDefaultToAsc = $albumCollection->setSort($this->validTestCases['photoIdXsmallThumbs']['reverse']);
        $this->assertEqual($shouldDefaultToAsc, 'asc');
        $shouldBeDesc = $albumCollection->setSort($this->validTestCases['photoIdDateReverseTableLimit']['reverse']);
        $this->assertEqual($shouldBeDesc, 'desc');
    }

    public function testSetDefaultLimitIfNeeded() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        $albumCollection->setProperties($this->validTestCases['photoIdXsmallThumbs']);
        $shouldHaveNoLimit = $albumCollection->setDefaultLimitIfNeeded();
        $this->assertEqual($shouldHaveNoLimit, null);

        $albumCollection->setProperties($this->validTestCases['photo160RandomListCaption']);
        $shouldHaveDefaultLimit = $albumCollection->setDefaultLimitIfNeeded();
        $this->assertEqual($shouldHaveDefaultLimit, 'limit 9');

        $albumCollection->setProperties($this->validTestCases['photoIdDateReverseTableLimit']);
        $shouldHaveUserLimit = $albumCollection->setDefaultLimitIfNeeded();
        $this->assertEqual($shouldHaveUserLimit, 'limit 3');
    }

    public function testSetWhereClause() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        $albumCollection->setProperties($this->validTestCases['photo160RandomListCaption']);
        $shouldNotHaveWhereClause = $albumCollection->setWhereClause();
        $this->assertEqual($shouldNotHaveWhereClause, null);

        $albumCollection->setProperties($this->validTestCases['photoIdXsmallThumbs']);
        $shouldHaveWhereClause = $albumCollection->setWhereClause();
        $this->assertEqual($shouldHaveWhereClause, 'where id in (1,2,3)');
    }

    public function testSetOrderByClause() {
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);

        $albumCollection->setProperties($this->validTestCases['photoIdXsmallThumbs']);
        $shouldNotHaveOrderByClause = $albumCollection->setOrderByClause();
        $this->assertEqual($shouldNotHaveOrderByClause, null);

        $albumCollection->setProperties($this->validTestCases['albumIdUserClear']);
        $shouldNotHaveOrderByClause = $albumCollection->setOrderByClause();
        $this->assertEqual($shouldNotHaveOrderByClause, null);

        $albumCollection->setProperties($this->validTestCases['photoIdDateReverseTableLimit']);
        $shouldHaveOrderByClause = $albumCollection->setOrderByClause();
        $this->assertEqual($shouldHaveOrderByClause, 'order by pubDate desc');
    }

    public function testGetData() {
      /*  for getting actual data
        require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php');
        $toppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
        $dbFacade = new ToppaDatabaseFacadeWp($toppaAutoLoader);
        $albumCollection = new Lib_ShashinAlbumCollection($dbFacade, $this->clonableAlbum);
      */

        $this->dbFacade->setReturnValue('sqlSelectMultipleRows', $this->albumData);
        $albumCollection = new Lib_ShashinAlbumCollection($this->dbFacade, $this->clonableAlbum);
        $rows = $albumCollection->getData();
        $this->assertEqual($this->albumData, $rows);
    }
    /*
    public function testValidateFormat() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setFormat($case['format']));
        }

        try {
            $this->albumCollection->setSize($this->invalidTestCases['invalid']['format']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateCaption() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setCaption($case['caption']));
        }

        try {
            $this->albumCollection->setCaption($this->invalidTestCases['invalid']['caption']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidatePosition() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setPosition($case['position']));
        }

        try {
            $this->albumCollection->setPosition($this->invalidTestCases['invalid']['position']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    public function testValidateClear() {
        foreach ($this->validTestCases as $case) {
            $this->assertTrue($this->albumCollection->setClear($case['clear']));
        }

        try {
            $this->albumCollection->setClear($this->invalidTestCases['invalid']['clear']);
            $this->fail("Exception was expected - invalid test case");
        }

        catch (Exception $e) {
            $this->pass("received expected invalid test case");
        }
    }

    */
}