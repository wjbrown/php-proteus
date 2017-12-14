<?php

namespace Proteus;

/**
 * Image Manipulation Class
 *
 * @author Wjbrown <wjbrown@gmail.com>
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

    public function resize ($type = 'fit', $width = 0, $height = 0, $params = [])
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

        $defaults = [
            'gravity' => 'c'
        ];

        $options = array_merge($defaults, $params);

        switch ($type) {
            case 'fit'     : $this->imagick->scaleImage($width, $height, true);          break;
            case 'force'   : $this->imagick->scaleImage($width, $height, false);         break;
            case 'adaptive': $this->imagick->adaptiveResizeImage($width, $height, true); break;
            case 'zoomCrop':
                if ($options['gravity'] == 'c') {
                    $this->imagick->cropThumbnailImage($width, $height);
                }
                // imagick's cropThumbnailImage ignores gravity for some reason
                else {
                    // if we are asking for an image that's a wider aspect-ratio than what we have
                    if (($width / $height) > ($this->getWidth() / $this->getHeight())) {
                        // resize based on width
                        $this->imagick->scaleImage($width, $this->getHeight(), true);
                        $this->cropByGravity($width, $height, $options['gravity']);
                    }
                    // else
                    else {
                        // resize based on width
                        $this->imagick->scaleImage($this->getWidth(), $height, true);
                        $this->cropByGravity($width, $height, $options['gravity']);
                    }
                }
        }
    }

    public function crop($width, $height, $x, $y)
    {
        $this->imagick->cropImage($width, $height, $x, $y);
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
