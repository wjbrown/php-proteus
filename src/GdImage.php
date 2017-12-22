<?php

namespace Proteus;

/**
 * Image Manipulation Class
 *
 * @author Wjbrown <wjbrown@gmail.com>
 */
class GdImage extends Image
{

    private $resource;

    private $image_type;

    public function open($file_path)
    {
        $this->image_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        switch ($this->image_type) {
            case 'png':
                $this->resource = imagecreatefrompng($file_path);
                break;
            case 'jpg':
            case 'jpeg':
                $this->resource = imagecreatefromjpeg($file_path);
                break;
            case 'gif':
                $this->resource = imagecreatefromgif($file_path);
                break;
        }
    }

    public function saveAs($file_path)
    {
        switch ($this->getImageType()) {
            case 'png':
                imagepng($this->resource, $file_path);
                break;
            case 'jpg':
                imagejpeg($this->resource, $file_path);
                break;
            case 'gif':
                imagegif($this->resource, $file_path);
                break;
        }
    }

    public function getImageType()
    {
        return $this->image_type;
    }

    public function setImageType($type)
    {
        $this->$image_type = $type;
    }

    public function getWidth()
    {
        return imagesx($this->resource);
    }

    public function getHeight()
    {
        return imagesy($this->resource);
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

        // make the width/height
        switch ($type) {
            case 'fit'     :
                // height is the limiting factor
                if (($width/$height) > ($this->getWidth()/$this->getHeight())) {
                    $this->resource = imagescale($this->resource, $width * ($height/$this->getHeight()), $height);
                }
                // width is the limiting factor
                else {
                    $this->resource = imagescale($this->resource, $width, $height * ($width/$this->getWidth()));
                }
                break;
            case 'force'   : 
                $this->resource = imagescale($this->resource, $width, $height);
                break;
            case 'crop'    :
                // if we are asking for an image that's a wider aspect-ratio than what we have
                if (($width / $height) > ($this->getWidth() / $this->getHeight())) {
                    // resize based on width
                    $this->resource = imagescale($width, $height * ($width/$this->getWidth()));
                    $this->cropByGravity($width, $height, $options['gravity']);
                }
                // else
                else {
                    // resize based on width
                    $this->resource = imagescale($width * ($height/$this->getHeight()), $height);
                    $this->cropByGravity($width, $height, $options['gravity']);
                }
        }
    }

    public function crop($width, $height, $x, $y)
    {
        $this->resource = imagecrop(
            $this->resource,
            [
                'x' => $x,
                'y' => $y,
                'width' => $width,
                'height' => $height
            ]
        );
    }

    public function sharpen ($type = 'default', $radius = 0, $sigma = 1)
    {
        // define the sharpen matrix
        $sharpen = [
            [0, -1, 0],
            [-1, 5, -1],
            [0, -1, 0]
        ];

        // calculate the sharpen divisor
        $divisor = array_sum(array_map('array_sum', $sharpen));

        // apply the matrix
        imageconvolution($this->resource, $sharpen, $divisor, 0);
    }

    public function __toString()
    {
        switch ($this->getImageType()) {
            case 'png':
                imagepng($this->resource);
                break;
            case 'jpg':
                imagejpeg($this->resource);
                break;
            case 'gif':
                imagegif($this->resource);
                break;
        }
    }

}
