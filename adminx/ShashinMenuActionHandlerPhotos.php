<?php

class Admin_ShashinMenuActionHandlerPhotos {
    private $functionsFacade;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct() {
    }

    public function setFunctionsFacade(Lib_ShashinFunctionsFacade $functionsFacade) {
        $this->functionsFacade = $functionsFacade;
        return $this->functionsFacade;
    }

    public function setMenuDisplayer(Admin_ShashinMenuDisplayer $menuDisplayer) {
        $this->menuDisplayer = $menuDisplayer;
        return $this->menuDisplayer;
    }

    public function setAdminContainer(Admin_ShashinContainer $adminContainer) {
        $this->adminContainer = $adminContainer;
        return $this->adminContainer;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function run() {
        $message = null;

        if (isset($this->request['switchingFromAlbumsMenu'])) {
            $this->functionsFacade->checkAdminNonceFields("shashinNoncePhotosMenu_" . $this->request['id']);
        }

        if (isset($this->request['shashinAction']) && $this->request['shashinAction'] == 'updateIncludeInRandom') {
            $this->functionsFacade->checkAdminNonceFields("shashinNonceUpdate", "shashinNonceUpdate");
            $message = $this->runUpdateIncludeInRandom();
        }

        return $this->menuDisplayer->run($message);
    }

    public function runUpdateIncludeInRandom() {
        //the order is important, as the default is 'user', which won't work in this context
        $shortcodeMimic = array('id' => $this->request['id'], 'type' => 'albumphotos', 'order' => 'source');
        $photos = Admin_ShashinContainer::getDataObjectCollection($shortcodeMimic);

        foreach ($photos as $photo) {
            $photoData = array('includeInRandom'=> $this->request['includeInRandom'][$photo->id]);
            $photo->set($photoData);
            $photo->flush();
        }

        return __('Updated "Include In Random" settings', 'shashin');
    }
}

