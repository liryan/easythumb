<?php
namespace EasyThumb\Genimg;

class JPGThumb extends Thumb{
    public function createFromFile($file){
        return imagecreatefrompng ($file);
    }
    public function save($res,$file){
        imagepng($res,$file);
    }
}
