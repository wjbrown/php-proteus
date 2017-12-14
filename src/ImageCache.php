<?php

namespace Proteus;

/**
 * Image Caching Class
 *
 * @author Wjbrown <wjbrown@gmail.com>
 *
 */
class ImageCache
{

    const DS = '/';

    private $path = self::DS . 'tmp';

    public function __construct($settings)
    {
        if (isset($settings['path'])) {
            $this->setPath($settings['path']);
        }
    }

    private function setPath($path)
    {
        if (is_dir($path) && is_writable($path)) {
            $this->path = realpath($path);
        }
        else {
            throw new ImageCacheException ("Directory $path does not exist or is not writable.");
        }
    }

    public function write($key, $data)
    {
        file_put_contents($this->path . self::DS . $key, $data);
    }

    public function read($key)
    {
        if ($data = file_get_contents($this->path . self::DS . $key)) {
            return $data;
        }
        else {
            return null;
        }
    }

    public function remember ($key, $callback)
    {
        if (!($image = $this->read($key))) {
            $image = $callback();
            $this->write($key, $image);
        }
        return $image;
    }

    public function delete($key)
    {
        unlink($this->path . self::DS . self.$key);
    }

    public function clear()
    {
        foreach (glob($this->path . self::DS . '*') as $file) {
            unlink($file);
        }
    }

    public function gc()
    {
        // Why do this with php when a crontab will suffice?

        # delete cached files if they were last accessed more than 14 days ago
        # 0 * * * * find /path/to/cache/dirs -type f -atime +14 -delete
    }

}

class ImageCacheException extends \Exception {

}
