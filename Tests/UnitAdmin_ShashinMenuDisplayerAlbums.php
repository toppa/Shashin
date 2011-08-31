<?php
require_once(dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaFunctionsFacadeWp.php');
require_once(dirname(__FILE__) . '/../Admin/ShashinMenuDisplayer.php');
require_once(dirname(__FILE__) . '/../Admin/ShashinMenuDisplayerAlbums.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinDataObjectCollection.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbum.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinAlbumCollection.php');
require_once(dirname(__FILE__) . '/../Lib/ShashinContainer.php');
require_once(dirname(__FILE__) . '/../Public/ShashinContainer.php');
Mock::generate('ToppaFunctionsFacadeWp');
Mock::generate('Lib_ShashinAlbumCollection');
Mock::generate('Public_ShashinContainer');
Mock::generate('Lib_ShashinAlbum');

class UnitAdmin_ShashinMenuDisplayerAlbums extends UnitTestCase {
    private $menuDisplayer;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $this->menuDisplayer = new Admin_ShashinMenuDisplayerAlbums();
    }

    // parent class tests
    public function testSetFunctionsFacade() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $result = $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $this->assertEqual($functionsFacade, $result);
    }

    public function testSetRequest() {
        $request = array('shashinOrderBy' => 'title', 'reverse' => 'n');
        $result = $this->menuDisplayer->setRequest($request);
        $this->assertEqual($request, $result);
    }

    public function testSetCollection() {
        $albumCollection = new MockLib_ShashinAlbumCollection();
        $result = $this->menuDisplayer->setCollection($albumCollection);
        $this->assertEqual($albumCollection, $result);
    }

    public function testSetContainer() {
        $container = new MockPublic_ShashinContainer();
        $result = $this->menuDisplayer->setContainer($container);
        $this->assertEqual($container, $result);
    }

    //public function testRun() {
    //}

    public function testMimicShortcodeWithDefaultValues() {
        $result = $this->menuDisplayer->mimicShortcode();
        $expectedResult = array('order' => 'title', 'reverse' => 'n');
        $this->assertEqual($expectedResult, $result);
    }

    public function testMimicShortcodeWithOrderByDateReverse() {
        $this->menuDisplayer->setRequest(array('shashinOrderBy' => 'date', 'shashinReverse' => 'y'));
        $result = $this->menuDisplayer->mimicShortcode();
        $expectedResult = array('order' => 'date', 'reverse' => 'y');
        $this->assertEqual($expectedResult, $result);
    }

    // not useful to test - would be all mocks
    //public function testGetDataObjects() {
    //}

    //public function testCheckOrderByNonce() {
    //}

    public function testSetSortAndOrderByUrlOrderByTitle() {
        $request['shashinOrderBy'] = null;
        $request['shashinReverse'] = null;
        $expectedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinReverse=y';
        $this->menuDisplayer->setRequest($request);
        $url = $this->menuDisplayer->setSortArrowAndOrderByUrl('title');
        $this->assertEqual($expectedUrl, $url);
        $this->assertEqual('&darr;', $this->menuDisplayer->getSortArrow());
    }

    public function testSetSortAndOrderByUrlReverseOrderByTitle() {
        $request['shashinOrderBy'] = 'title';
        $request['shashinReverse'] = 'y';
        $expectedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinReverse=n';
        $this->menuDisplayer->setRequest($request);
        $url = $this->menuDisplayer->setSortArrowAndOrderByUrl('title');
        $this->assertEqual($expectedUrl, $url);
        $this->assertEqual('&uarr;', $this->menuDisplayer->getSortArrow());
    }

    //public function testSetOrderByNonce() {
    //}

    //child class tests
    public function testGenerateOrderByLink() {
        $noncedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinOrderBy=title&amp;shashinReverse=y&amp;_wpnonce=25c96e77ab';
        $expectedLink = "<a href=\"$noncedUrl\">Title &darr;</a>";
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('addNonceToUrl', $noncedUrl);
        $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $link = $this->menuDisplayer->generateOrderByLink('title', 'Title');
        $this->assertEqual($expectedLink, $link);
    }

    public function testGenerateSyncLink() {
        $noncedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinAction=syncAlbum&amp;id=1&amp;_wpnonce=5da8b799f6';
        $imageUrl = 'http://localhost/wordpress/wp-content/plugins/shashin3alpha/Admin/Display/images/arrow_refresh.png';
        $expectedLink =
            '<a href="'
            . $noncedUrl
            . '"><img src="'
            . $imageUrl
            . '" alt="Sync Album" width="16" height="16" border="0" /></a>';
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('addNonceToUrl', $noncedUrl);
        $functionsFacade->setReturnValue('getPluginsUrl', $imageUrl);
        $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $album = new MockLib_ShashinAlbum();
        $album->setReturnValue('__get', 1, array('id'));
        $link = $this->menuDisplayer->generateSyncLink($album);
        $this->assertEqual($expectedLink, $link);
    }

    public function testGenerateDeleteLink() {
        $noncedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinAction=deleteAlbum&amp;id=1&amp;_wpnonce=5da8b799f6';
        $imageUrl = 'http://localhost/wordpress/wp-content/plugins/shashin3alpha/Admin/Display/images/delete.png';
        $onClickText = 'Are you sure you want to delete this album? Any shashin tags for displaying this album will be permanently broken';
        $expectedLink =
            '<a href="'
            . $noncedUrl
            . "\" onclick=\"return confirm('$onClickText')\">"
            . '<img src="'
            . $imageUrl
            . '" alt="Sync Album" width="16" height="16" border="0" /></a>';
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('addNonceToUrl', $noncedUrl);
        $functionsFacade->setReturnValue('getPluginsUrl', $imageUrl);
        $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $album = new MockLib_ShashinAlbum();
        $album->setReturnValue('__get', 1, array('id'));
        $link = $this->menuDisplayer->generateDeleteLink($album);
        $this->assertEqual($expectedLink, $link);
    }

    public function testGenerateSyncAllLink() {
        $noncedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinAction=syncAllAlbums&amp;_wpnonce=25c96e77ab';
        $expectedLink = '<a href="' . $noncedUrl . '">Sync All</a>';
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('addNonceToUrl', $noncedUrl);
        $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $link = $this->menuDisplayer->generateSyncAllLink();
        $this->assertEqual($expectedLink, $link);
    }

    public function testGeneratePhotosMenuSwitchLink() {
        $noncedUrl = '?page=Shashin3AlphaToolsMenu&amp;shashinMenu=photos&amp;switchingFromAlbumsMenu=1&amp;id=1&amp;_wpnonce=5da8b799f6';
        $album = new MockLib_ShashinAlbum();
        $album->setReturnValue('__get', 1, array('id'));
        $album->setReturnValue('__get', "1999 - Mike and Maria's Wedding", array('title'));
        $expectedLink = '<a href="' . $noncedUrl . '">1999 - Mike and Maria\'s Wedding</a>';
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('addNonceToUrl', $noncedUrl);
        $this->menuDisplayer->setFunctionsFacade($functionsFacade);
        $link = $this->menuDisplayer->generatePhotosMenuSwitchLink($album);
        $this->assertEqual($expectedLink, $link);
    }

}
