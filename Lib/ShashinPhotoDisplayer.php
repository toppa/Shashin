<?php


abstract class Lib_ShashinPhotoDisplayer {
    protected $photo;
    protected $requestedSize;
    protected $numericSize;
    protected $actualSize;
    protected $requestedCropped = false;
    protected $displayCropped = false;
    protected $imgHeight;
    protected $imgWidth;
    protected $imgSrc;
    protected $imgAltAndTitle;
    protected $imgClass;
    protected $imgTag;
    protected $validSizes = array();
    protected $validCropSizes = array();
    protected $sizesMap = array();

    /*
        'flickr' => array(
            'xsmall' => 75,
            'small' => 100,
            'medium' => 240,
            'large' => 500,
            'xlarge' => 1024,
        ),
        'twitpic' => array(
            'xsmall' => 75,
            'small' => 150,
            'medium' => 150,
            'large' => 600,
            'xlarge' => 600,
        )

 */

    public function __construct(Lib_ShashinPhoto $photo) {
        $this->photo = $photo;

        if (!$_SESSION['shashin_id_counter']) {
            $_SESSION['shashin_id_counter'] = 1;
        }
    }

    public function setRequestedSize($requestedSize) {
        $this->requestedSize = $requestedSize;
    }

    public function setRequestedCropped($requestedCropped) {
        $this->requestedCropped = $requestedCropped;
    }

    public function run() {
        try {
            $this->setNumericSizeFromRequestedSize();
            $this->checkNumericSizeIsNumeric();
            $this->setActualSizeFromValidSizes();
            $this->setDisplayCroppedIfRequested();
            $this->setImgWidthAndHeight();
            $this->setImgSrc();
            $this->setImgAltAndTitle();
            $this->setImgTag();
            $_SESSION['shashin_id_counter']++;
        }

        catch (Exception $e) {
            return "<strong>" . $e->getMessage() . "</strong>";
        }

        return $this->imgTag;
//<img src="' . $photo['enclosure_url'] . '?imgmax=72&amp;crop=1" />

    }

    public function setNumericSizeFromRequestedSize() {
        if (array_key_exists($this->requestedSize, $this->sizesMap)) {
            $this->numericSize = $this->sizesMap[$this->requestedSize];
        }

        else {
            $this->numericSize = $this->requestedSize;
        }

        return true;
    }

    public function checkNumericSizeIsNumeric() {
        if (!is_numeric($this->numericSize)) {
            throw New Exception("invalid size requested");
        }

        return true;
    }

    public function setActualSizeFromValidSizes() {
        foreach ($this->validSizes as $size) {
            if ($this->numericSize <= $size) {
                $this->actualSize = $size;
                break;
            }
        }

        return true;
    }

    public function setDisplayCroppedIfRequested() {
        if ($this->requestedCropped) {
            if (in_array($this->actualSize, $this->validCropSizes)) {
                $this->displayCropped = true;
            }
        }

        return true;
    }

    public function setImgWidthAndHeight() {
        if ($this->displayCropped) {
            $this->imgWidth = $this->actualSize;
            $this->imgHeight = $this->actualSize;
        }

        // see if actualSize should be applied to the height or the width
        elseif ($this->photo->width > $this->photo->height) {
            $this->imgWidth = $this->actualSize;
            $percentage = $this->actualSize / $this->photo->width;
            $this->imgHeight = $percentage * $this->photo->height;
            settype($this->imgHeight, "int"); // drop any decimals
        }

        else {
            $this->imgHeight = $this->actualSize;
            $percentage = $this->actualSize / $this->photo->height;
            $this->imgWidth = $percentage * $this->photo->width;
            settype($this->imgWidth, "int"); // drop any decimals
        }

        return true;
    }

    abstract public function setImgSrc();

    public function setImgAltAndTitle() {
        // there may already be entities in the description, so we want to be
        // conservative with what we replace
        $this->imgAltAndTitle = str_replace('"', '&quot;', $this->photo->description);
    }

    public function setImgClass($class) {
        $this->imgClass = $class;
    }

    public function setImgTag() {
        $this->imgTag =
            '<img src="' . $this->imgSrc
            . '" alt="' . $this->imgAltAndTitle
            . '" title="' . $this->imgAltAndTitle
            . '" width="' . $this->imgWidth
            . '" height="' . $this->imgHeight
            . '" id="shashin_thumb_image_' . $_SESSION['shashin_id_counter'] . '"';

        if ($this->imgClass) {
            $this->imgTag .= ' class="' . $this->imgClass . '"';
        }

        $this->imgTag .= ' />';
    }
}
