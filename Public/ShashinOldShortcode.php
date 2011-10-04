<?php

class Public_ShashinOldShortcode {
    private $content;
    private $container;
    private $request;

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
        $this->parseSingleImage();
        $this->parseRandomImages();
        return $this->content;
    }

    public function parseSingleImage() {
        $sImage = "/\[simage=(\d+),(\d{2,4}|max),?(\w?),?(\w{0,6}),?(\w{0,5}),?(\d*)\]/";

        if (preg_match_all($sImage, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('ToppaFunctions', 'strtolowerCallback'));
                $arrayShortcode = array(
                    'type' => 'photo',
                    'id' => $match[1],
                    'size' => $match[2],
                    'caption' => $match[3],
                    'position' => $match[4],
                    'clear' => $match[5],
                    'thumbnail' => $match[6]);
                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseRandomImages() {
        $sRandom = "/\[srandom=([\w\|]+),(\d{2,4}|max),(\d+|max),(\d+),?(\w?),?(\w{0,6}),?(\w{0,5})\]/";

        if (preg_match_all($sRandom, $this->content, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                array_walk($match, array('ToppaFunctions', 'strtolowerCallback'));

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
                    'size' => $match[2],
                    'columns' => $match[3],
                    'limit' => $match[4],
                    'caption' => $match[5],
                    'position' => $match[6],
                    'clear' => $match[7]
                );

                $markup = $this->parseShortcode($arrayShortcode);
                $this->content = str_replace($match[0], $markup, $this->content);
            }
        }

        return $this->content;
    }

    public function parseShortcode($arrayShortcode) {
        $shortcode = $this->container->getShortcode($arrayShortcode);
        $dataObjectCollection = $this->container->getClonablePhotoCollection();
        $layoutManager = $this->container->getLayoutManager($shortcode, $dataObjectCollection, $this->request);
        return $layoutManager->run();
    }
}
