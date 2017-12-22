<?php

namespace Proteus;

/**
 * Image Manipulation Class
 *
 * @author Wjbrown <wjbrown@gmail.com>
 */
abstract class Image
{

    private $img;

    public static $gd = false;

    public static function create($file_path)
    {
        if (self::$gd) {
            $img = new GdImage($file_path);
        }
        else {
            $img = extension_loaded('imagick') ? new ImagickImage($file_path) : new GdImage($file_path);
        }
        
        return $img;
    }

    protected function __construct($file_path)
    {
        $this->open($file_path);
    }

    abstract public function open($file_path);

    abstract public function saveAs($file_path);

    abstract public function getImageType();

    abstract public function setImageType($type);

    abstract public function getWidth();

    abstract public function getHeight();

    abstract public function resize ($type = 'fit', $width = 0, $height = 0, $params = []);

    abstract public function crop($width, $height, $x, $y);

    abstract public function sharpen ($type = 'default', $radius = 0, $sigma = 1);

    abstract public function __toString();

    public function getExt()
    {
        return strtolower($this->getImageType());
    }

    public function cropByGravity($width, $height, $gravity)
    {
        switch ($gravity) {
            case 'n':
                $x = ($this->getWidth() / 2) - ($width / 2);
                $y = 0;
                break;
            case 'ne':
                $x = $this->getWidth() - $width;
                $y = 0;
                break;
            case 'e':
                $x = $this->getWidth() - $width;
                $y = ($this->getHeight() / 2) - ($height / 2);
                break;
            case 'se':
                $x = $this->getWidth() - $width;
                $y = $this->getHeight() - $height;
                break;
            case 's':
                $x = ($this->getWidth() / 2) - ($width / 2);
                $y = $this->getHeight() - $height;
                break;
            case 'sw':
                $x = 0;
                $y = $this->getHeight() - $height;
                break;
            case 'w':
                $x = 0;
                $y = ($this->getHeight() / 2) - ($height / 2);
                break;
            case 'nw':
                $x = 0;
                $y = 0;
                break;
            default:    // 'center'
                $x = ($this->getWidth() / 2) - ($width / 2);
                $y = ($this->getHeight() / 2) - ($height / 2);
        }

        return $this->crop($width, $height, $x, $y);
    }

    public static function getContentType($blob)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($blob);
    }

}
