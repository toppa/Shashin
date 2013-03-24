<?php

class Public_ShashinOldShortcode {
    private $content;
    private $container;
    private $request;
    private $cropSizes = array(32, 48, 64, 72, 104, 144, 150, 160);

    public function __construct() {
    }

    public function setContent($content) {
        $this->content = $content;
        return $this->content;
    }

    public function setContainer($container) {
        $this->container = $container;
        return $this->container;
    }

    public function setRequest(array $request) {
        $this->request = $request;
        return $this->request;
    }

    public function run() {
        $this->parseSinglePhoto();
        $this->parseRandomPhotos();
        $this->parseSpecifiedPhotos();
        $this->parseNewestPhotos();
        $this->parseSpecifiedAlbumPhotos();
        $this->parseSpecifiedAlbums();
        $this->parseSpecifiedAlbumsList();
        return $this->content;
    }

    public function parseSinglePhoto() {
        $sImage = "/\[simage=(\d+),(\d{2,4}|max),?(\w?),?(\w{0,6}),?(\w{0,5}),?(\d*)\]/";

        if (preg_match_all($sImage, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $caption = $this->setCaption($match[3]);
                $crop = $this->setCrop($match[2]);

                $arrayShortcode = array(
                    'type' => 'photo',
                    'id' => $match[1],
                    'size' => $match[2],
                    'crop' => $crop,
                    'columns' => 1,
                    'caption' => $caption,
                    'position' => $match[4],
                    'clear' => $match[5],
                    'thumbnail' => $match[6]);
                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseRandomPhotos() {
        $sRandom = "/\[srandom=([\w\|]+),(\d{2,4}|max),(\d+|max),(\d+),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($sRandom, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $caption = $this->setCaption($match[5]);
                $crop = $this->setCrop($match[2]);

                if ($match[1] == 'any') {
                    $type = 'photo';
                    $id = null;
                }

                else {
                    $type = 'albumphotos';
                    $id = str_replace('|', ',', $match[1]);
                }

                $arrayShortcode = array(
                    'type' => $type,
                    'id' => $id,
                    'caption' => $caption,
                    'order' => 'random',
                    'size' => $match[2],
                    'crop' => $crop,
                    'columns' => $match[3],
                    'limit' => $match[4],
                    'position' => $match[6],
                    'clear' => $match[7]
                );

                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseSpecifiedPhotos() {
        $sThumbs = "/\[sthumbs=([\d\|]+),(\d{2,4}|max),(\d+|max),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($sThumbs, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $id = str_replace('|', ',', $match[1]);
                $caption = $this->setCaption($match[4]);
                $crop = $this->setCrop($match[2]);
                $arrayShortcode = array(
                    'type' => 'photo',
                    'id' => $id,
                    'caption' => $caption,
                    'order' => 'user',
                    'size' => $match[2],
                    'crop' => $crop,
                    'columns' => $match[3],
                    'position' => $match[5],
                    'clear' => $match[6]
                );

                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseNewestPhotos() {
        $sNewest = "/\[snewest=([\w\|]+),(\d{2,4}|max),(\d+|max),(\d+),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($sNewest, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $caption = $this->setCaption($match[5]);
                $crop = $this->setCrop($match[2]);

                if ($match[1] == 'any') {
                    $type = 'photo';
                    $id = null;
                }

                else {
                    $type = 'albumphotos';
                    $id = str_replace('|', ',', $match[1]);
                }

                $arrayShortcode = array(
                    'type' => $type,
                    'id' => $id,
                    'caption' => $caption,
                    'order' => 'date',
                    'reverse' => 'y',
                    'size' => $match[2],
                    'crop' => $crop,
                    'columns' => $match[3],
                    'limit' => $match[4],
                    'position' => $match[6],
                    'clear' => $match[7]
                );

                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseSpecifiedAlbumPhotos() {
        $sAlbumPhotos = "/\[salbumphotos=(\d+),(\d{2,4}|max),(\d+|max),?(\w?),?(\w?),?([\w ]{0,}),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($sAlbumPhotos, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $id = str_replace('|', ',', $match[1]);
                $caption = $this->setCaption($match[4]);
                $crop = $this->setCrop($match[2]);
                list($order, $reverse) = explode(' ', $match[6]);
                $order = $this->setOrder($order);
                $reverse = $this->setReverse($reverse);
                $arrayShortcode = array(
                    'type' => 'albumphotos',
                    'id' => $id,
                    'caption' => $caption,
                    'order' => $order,
                    'reverse' => $reverse,
                    'size' => $match[2],
                    'crop' => $crop,
                    'columns' => $match[3],
                    'position' => $match[7],
                    'clear' => $match[8]
                );
                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseSpecifiedAlbums() {
        $sAlbumThumbs = "/\[salbumthumbs=([\w\|\ ]+),(\d+|max),?(\w?),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";
        if (preg_match_all($sAlbumThumbs, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $arrayShortcode = $this->initializeArrayShortcodeForAlbums($match[1]);
                $arrayShortcode['caption'] = ($match[3] == 'y' || $match[4] == 'y') ? 'y' : 'n';
                $arrayShortcode['size'] = 160;
                $arrayShortcode['crop'] = 'y';
                $arrayShortcode['columns'] = $match[2];
                $arrayShortcode['position'] = $match[5];
                $arrayShortcode['clear'] = $match[6];
                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseSpecifiedAlbumsList() {
        $sAlbumList = "/\[salbumlist=([\w+\|\ ]+),?(\w?)\]/";

        if (preg_match_all($sAlbumList, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('Lib_ShashinFunctions', 'strtolowerCallback'));
                $arrayShortcode = $this->initializeArrayShortcodeForAlbums($match[1]);
                $arrayShortcode['caption'] = $match[2];
                $arrayShortcode['size'] = 160;
                $arrayShortcode['crop'] = 'y';
                $arrayShortcode['columns'] = 1;
                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseShortcode($arrayShortcode) {
        $shortcode = $this->container->getShortcode($arrayShortcode);

        switch ($arrayShortcode['type']) {
            case 'album':
                $methodToCall = 'getClonableAlbumCollection';
                break;
            case 'albumphotos':
                $methodToCall = 'getClonableAlbumPhotosCollection';
                break;
            case 'photo':
            default:
                $methodToCall = 'getClonablePhotoCollection';
        }

        $dataObjectCollection = $this->container->$methodToCall();
        $layoutManager = $this->container->getLayoutManager($shortcode, $dataObjectCollection, $this->request);
        return $layoutManager->run();
    }

    public function setCaption($caption) {
        return ($caption == 'c') ? 'n' : $caption;
    }

    public function setOrder($oldOrder) {
        switch($oldOrder) {
            case 'pub_date':
            case 'taken_timestamp':
                $newOrder = 'date';
                break;
            case 'uploaded_timestamp':
                $newOrder = 'uploaded';
                break;
            case 'last_updated':
                $newOrder = 'sync';
                break;
            case 'picasa_order':
                $newOrder = 'source';
                break;
            default:
                $newOrder = $oldOrder;
        }

        return $newOrder;
    }

    public function setReverse($reverse) {
        return ($reverse == 'desc') ? 'y' : 'n';
    }

    public function setCrop($size) {
        return (in_array($size, $this->cropSizes)) ? 'y' : 'n';
    }

    public function initializeArrayShortcodeForAlbums($orderOrId) {
        $arrayShortcode = array('type' => 'album');
        $doesOrderOrIdContainOnlyIds = str_replace('|', '', $orderOrId);

        if (is_numeric($doesOrderOrIdContainOnlyIds)) {
            $arrayShortcode['id'] = str_replace('|', ',', $orderOrId);
            $arrayShortcode['order'] = 'user';
            $arrayShortcode['reverse'] = 'n';
        }

        else {
            $arrayShortcode['limit'] = 300;
            list($order, $reverse) = explode(' ', $orderOrId);
            $arrayShortcode['order'] = $this->setOrder($order);
            $arrayShortcode['reverse'] = $this->setReverse($reverse);
        }

        return $arrayShortcode;
   }
}
