<?php

namespace Proteus;

/**
 * Image Manipulation Class
 *
 * @author Wjbrown <wjbrown@gmail.com>
 */
class ImagickImage extends Image
{

    private $resource;

    public function open($file_path)
    {
        $this->resource = new \Imagick($file_path);
    }

    public function saveAs($file_path)
    {
        return $this->resource->writeImageFile($file_path);
    }

    public function getImageType()
    {
        return $this->resource->getImageFormat();
    }

    public function setImageType($type)
    {
        $this->resource->setImageFormat($type);
    }

    public function getWidth()
    {
        return $this->resource->getImageWidth();
    }

    public function getHeight()
    {
        return $this->resource->getImageHeight();
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
            case 'fit'     : $this->resource->scaleImage($width, $height, true);          break;
            case 'force'   : $this->resource->scaleImage($width, $height, false);         break;
            case 'adaptive': $this->resource->adaptiveResizeImage($width, $height, true); break;
            case 'crop'    :
                if ($options['gravity'] == 'c') {
                    $this->resource->cropThumbnailImage($width, $height);
                }
                // resource's cropThumbnailImage ignores gravity for some reason
                else {
                    // if we are asking for an image that's a wider aspect-ratio than what we have
                    if (($width / $height) > ($this->getWidth() / $this->getHeight())) {
                        // resize based on width
                        $this->resource->scaleImage($width, $this->getHeight(), true);
                        $this->cropByGravity($width, $height, $options['gravity']);
                    }
                    // else
                    else {
                        // resize based on width
                        $this->resource->scaleImage($this->getWidth(), $height, true);
                        $this->cropByGravity($width, $height, $options['gravity']);
                    }
                }
        }
    }

    public function crop($width, $height, $x, $y)
    {
        $this->resource->cropImage($width, $height, $x, $y);
    }

    public function sharpen ($type = 'default', $radius = 0, $sigma = 1)
    {
        if ($type == 'default') {
            $this->resource->sharpenImage($radius, $sigma);
        }
        else {
            $this->resource->adaptiveSharpenImage($radius, $sigma);
        }
    }

    public function __toString()
    {
        return $this->resource->__toString();
    }

}
