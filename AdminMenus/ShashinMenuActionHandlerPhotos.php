<?php
/**
 * Created by JetBrains PhpStorm.
 * User: toppa
 * Date: 4/27/11
 * Time: 8:04 AM
 * To change this template use File | Settings | File Templates.
 */

class ShashinMenuActionHandlerPhotos {
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
            if ($this->requests['shashinAction'] == 'updateIncludeInRandom') {
                $this->functionsFacade->checkAdminNonceFields("shashinNonceUpdate", "shashinNonceUpdate");
                $message = $this->runUpdateIncludeInRandom();
            }

            echo $this->menuDisplayer->run($message);
        }

        catch (Exception $e) {
            echo "<p>" . __("Shashin Error: ", "shashin") . $e->getMessage() . "</p>";
        }

        return true;
    }

    public function runUpdateIncludeInRandom() {
        $album = $this->objectsSetup->setupAlbum($this->requests['albumKey']);
        $photos = $album->getAlbumPhotos();

        foreach ($this->requests['includeInRandom'] as $k=>$v) {
            $fields = array('includeInRandom'=> $v);
            $photos[$k]->setPhoto($fields);
        }

        return __('Updated "Include In Random" settings', "shashin");
    }
 }
