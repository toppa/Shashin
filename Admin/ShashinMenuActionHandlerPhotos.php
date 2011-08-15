<?php

class Admin_ShashinMenuActionHandlerPhotos {
    private $functionsFacade;
    private $menuDisplayer;
    private $adminContainer;
    private $request;

    public function __construct(
      ToppaFunctionsFacade $functionsFacade,
      Admin_ShashinMenuDisplayer $menuDisplayer,
      Admin_ShashinContainer $adminContainer,
      array $request) {
        $this->functionsFacade = $functionsFacade;
        $this->menuDisplayer = $menuDisplayer;
        $this->adminContainer = $adminContainer;
        $this->request = $request;
    }

    public function run() {
        try {
            if ($this->request['switchingFromAlbumsMenu']) {
                $this->functionsFacade->checkAdminNonceFields("shashinNoncePhotosMenu_" . $this->request['id']);
            }

            if ($this->request['shashinAction'] == 'updateIncludeInRandom') {
                $this->functionsFacade->checkAdminNonceFields("shashinNonceUpdate", "shashinNonceUpdate");
                $message = $this->runUpdateIncludeInRandom();
            }

            return $this->menuDisplayer->run($message);
        }

        catch (Exception $e) {
            echo "<p>" . __("Shashin Error: ", "shashin") . $e->getMessage() . "</p>";
        }

        return true;
    }

    public function runUpdateIncludeInRandom() {
        //the order is important, as the default is 'user', which won't work in this context
        $shortcodeMimic = array('id' => $this->request['id'], 'type' => 'albumphotos', 'order' => 'source');
        $photos = $this->menuDisplayer->getDataObjects($shortcodeMimic);

        foreach ($photos as $photo) {
            $photoData = array('includeInRandom'=> $this->request['includeInRandom'][$photo->id]);
            $photo->set($photoData);
            $photo->flush();
        }

        return __('Updated "Include In Random" settings', 'shashin');
    }
}
