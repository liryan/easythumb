<?php
namespace EasyThumb\Genimg;

class JPGThumb extends Thumb{
    public function createFromFile($file){
        return imagecreatefromjpeg ($file);
    }
    public function save($file){
        imagejpeg($file);
    }
}
