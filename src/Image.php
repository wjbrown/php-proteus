<?php

namespace Proteus;

/**
 * Image Manipulation Class
 *
 * @author Wjbrown <wjbrown@gmail.com>
 *
 * @method Image ($filepath)
 * @method Image open($filepath)
 * @method Image saveAs($file, $quality)
 * @method Image getImageType()
 * @method Image setImageType()
 * @method Image getExt()
 * @method Image getWidth()
 * @method Image getHeight()
 * 
 * @method Image resize($type = 'fit', $width = null, $height = null)
 * @method Image crop($x, $y, $width, $height)
 * @method Image sharpen()
 * 
 * @method Image __toString()
 */
class Image
{

    private $file_path, $file_name, $file_ext;

    private $imagick;

    public function __construct($file_path)
    {
        $this->open($file_path);
    }

    public function open($file_path)
    {
        $this->file_path = $file_path;
        $this->file_name = basename($file_path);
        $this->file_ext = substr(strrchr($file_path, "."), 1);

        $this->imagick = new \Imagick($file_path);
    }

    public function saveAs($file_path)
    {
        return $this->imagick->writeImageFile($file_path);
    }

    public function getImageType()
    {
        return $this->imagick->getImageFormat();
    }

    public function setImageType($type)
    {
        $this->setImageFormat($type);
    }

    public function getExt()
    {
        return strtolower($this->getImageType());
    }

    public function getWidth()
    {
        return $this->imagick->getImageWidth();
    }

    public function getHeight()
    {
        return $this->imagick->getImageHeight();
    }

    public function resize ($type = 'fit', $width = 0, $height = 0)
    {
        if ($width == 0 && $height == 0) {
            return;
        }
        else if ($width == 0) {
           $width = ($height / $this->getHeight()) * $this->getWidth();
        }
        else if ($height == 0) {
           $height = ($width / $this->getWidth()) * $this->getHeight();
        }

        switch ($type) {
            case 'fit'     : $this->imagick->scaleImage($width, $height, true);          break;
            case 'force'   : $this->imagick->scaleImage($width, $height, false);         break;
            case 'adaptive': $this->imagick->adaptiveResizeImage($width, $height, true); break;
            // case 'thumbnail':
            // case 'crop':
            default        : $this->imagick->cropThumbnailImage($width, $height);
        }
    }

    public function crop($width, $height, $x, $y)
    {
        $this->imagick->cropImage($width, $height, $x, $y);
    }

    public function sharpen ($type = 'default', $radius = 0, $sigma = 1)
    {
        if ($type == 'default') {
            $this->imagick->sharpenImage($radius, $sigma);
        }
        else {
            $this->imagick->adaptiveSharpenImage($radius, $sigma);
        }
    }

    public function __toString()
    {
        return $this->imagick->__toString();
    }

    public static function getContentType($blob)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($blob);
    }

}
