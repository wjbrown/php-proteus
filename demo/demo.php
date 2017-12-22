<?php
require __DIR__ . '/../src/Image.php';
require __DIR__ . '/../src/ImagickImage.php';
require __DIR__ . '/../src/GdImage.php';
require __DIR__ . '/../src/ImageCache.php';

$imgcache = new \Proteus\ImageCache([
    'path'         => dirname(__FILE__) . '/img/cache',
]);

$imgcache->off();

$request           = new StdClass();
$request->filename = 'saturn.png';
$request->query    = [
    'type'   => 'crop',
    'w'      => '200',
    'h'      => '300',
    'g'      => 'se',
    's'      => 0
];

// remember, the $img here is something that could come from the callback OR a file.  its basically a string.
$cachekey = sha1(json_encode($request));
$img = $imgcache->remember($cachekey, function() use ($request) {

    $img = \Proteus\Image::create('img/' . $request->filename);
    $img->resize(
        $request->query['type'],
        $request->query['w'],
        $request->query['h'],
        [
            'gravity' => $request->query['g']
        ]
    );
    return $img;    // return the blob that is going to be cached

});

header('Content-type: ' . \Proteus\Image::getContentType((string)$img));
header('Cache-Control: max-age=324555');
echo $img;

