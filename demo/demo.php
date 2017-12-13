<?php
require('Image.php');
require('ImageCache.php');

\Proteus\ImageCache::config([
    'path' => dirname(__FILE__) . '/img/cache'
]);

$request           = new StdClass();
$request->filename = 'saturn.png';
$request->query    = [
    'resize' => 'thumbnail',
    'w'      => '600',
    'h'      => '600'
];

// remember, the $img here is something that could come from the callback OR a file.  its basically a string.
$img = \Proteus\ImageCache::remember($request, function() use ($request) {

    $img = new \Proteus\Image('img/' . $request->filename);
    $img->resize($request->query['w'], $request->query['h']);
    return $img;    // return the blob that is going to be cached

});

header('Content-type: ' . \Proteus\Image::getContentType($img));
header('Cache-Control: max-age=324555');
echo $img;

