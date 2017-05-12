<?php
namespace EasyThumb\Genimg;

class JPGThumb extends Thumb{
    public function createFromFile($file){
        return imagecreatefromjpeg ($file);
    }
    public function save($res,$file){
        imagejpeg($res,$file);
    }
}
