<?php
namespace EasyThumb\Genimg;

class GIFThumb extends Thumb{
    public function createFromFile($file){
        return imagecreatefromgif($file);
    }
    public function save($file){
        imagegif($file);
    }
}
