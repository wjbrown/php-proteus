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

    private static $path = self::DS . 'tmp';

    private static function setPath($path)
    {
        if (is_dir($path) && is_writable($path)) {
            self::$path = realpath($path);
        }
        else {
            throw new ImageCacheException ("Directory $path does not exist or is not writable.");
        }
    }

    public static function config($settings)
    {
        if (isset($settings['path'])) {
            self::setPath($settings['path']);
        }
    }

    public static function write($key, $data)
    {
        file_put_contents(self::$path . self::DS . $key, $data);
    }

    public static function read($key)
    {
        if ($data = file_get_contents(self::$path . self::DS . $key)) {
            return $data;
        }
        else {
            return null;
        }
    }

    public static function remember ($key, $callback)
    {
        $key = sha1(json_encode($key));
        if (!($image = self::read($key))) {
            $image = $callback();
            self::write($key, $image);
        }
        return $image;
    }

    public static function delete($key)
    {
        unlink(self::$path . self.DS . self.$key);
    }

    public static function clear()
    {
        foreach (glob(self::$path . self.DS . '*') as $file) {
            unlink($file);
        }
    }

    public static function gc()
    {
        // Why do this with php when a crontab will suffice?

        # delete cached files if they were last accessed more than 14 days ago
        # 0 * * * * find /path/to/cache/dirs -type f -atime +14 -delete
    }

}

class ImageCacheException extends \Exception {

}
