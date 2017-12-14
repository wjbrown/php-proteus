# PHP Proteus Image Library

[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](http://www.opensource.org/licenses/MIT)

## What it is

A set of classes that can be used to manipulate images and cache the results.  I tried to keep it pretty simple, so it only 
handles operations that I've found to be the most useful.  Several image manipulation functions were omitted b/c they seem
to be handled well with css.

I've got another repo that acts as a CDN, using these classes within a slim framework deployment.  https://github.com/wjbrown/php-proteus-slim

## What it does

Here's a short list of things it can do:
- resize
- - force
- - fit
- - adaptive
- - zoom-crop
- crop
- sharpen (I've found sharpen necessary after reducing the size of jpegs)

## Using this library

Simply clone using one of the following methods:

**SSH**

    git clone git@github.com:wjbrown/php-proteus.git YourProjectName
    
**HTTPS**

    git clone https://github.com/wjbrown/php-proteus.git YourProjectName
    
Then edit the composer.json file to set your own project meta information and define your dependencies as normal. A simple 
demo exists for reference.
