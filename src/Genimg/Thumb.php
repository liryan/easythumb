<?php
namespace EasyThumb\Genimg;
use EasyThumb\EasyThumb;
abstract class Thumb{
    private $width;
    private $height;
    private $extension;

    abstract public function createFromFile($file);
    abstract public function save($res,$file);

    private function setInfo($w,$h,$extension)
    {
        $this->width=$w;
        $this->height=$h;
        $this->extension=$extension;
    }

    public function extension()
    {
        return trim($this->extension,".");
    }

    public static function checkType($file,$limittype=0)
    {
        $define=[
           EasyThumb::GIF=>[IMAGETYPE_GIF,GIFThumb::class], 
           EasyThumb::PNG=>[IMAGETYPE_PNG,PNGThumb::class], 
           EasyThumb::JPG=>[IMAGETYPE_JPEG,JPGThumb::class], 
           EasyThumb::JPEG=>[IMAGETYPE_JPEG,JPGThumb::class], 
        ];

        list($width, $height, $type, $attr)=getimagesize($file);

        if($limittype > 0 && ($limittype & EasyThumb::BIN) == 0){
            foreach($define as $k=>$row){
                if( ($limittype & $k) > 0 && $type==$row[0]){
                    $thumb= new $row[1]; 
                    $thumb->setInfo($width,$height,image_type_to_extension($type));
                    return $thumb;
                }
            } 
            return null;
        }
        else{
            foreach($define as $row){
                if($type==$row[0]){
                    $thumb= new $row[1]; 
                    $thumb->setInfo($width,$height,image_type_to_extension($type));
                    return $thumb;
                }
            } 
            return null;
        }
    }

    public function createOutFile($w,$h,$color=0)
    {
        $gd=imagecreatetruecolor ( $w, $h);
        if(preg_match("/^0x[0-9ABCEEF]{6}$/i",$color)){
            $red=base_convert(substr(2,2),16,10);
            $green=base_convert(substr(4,2),16,10);
            $blue=base_convert(substr(6,2),16,10);
            $color=imagecolorallocate ($gd ,  $red , $green , $blue);
        }
        else{
            $color=imagecolorallocate ($gd , 0 , 0 , 0 );
        }
        imagefill($gd,0,0,$color);  
        return $gd;
    }

    public function toSize($file,$size)
    {
        if(!file_exists($file)){
            throw new Exception("File dosen't exist");
        }

        $width=$this->width;
        $height=$this->height;

        if($width == 0 || $height==0){
            throw new Exception("File size is error");
        }
        $gd=$this->createFromFile($file);
        foreach($size as $row){
            $w=$row['w'];
            $h=$row['h'];
            $target_w=0;
            $target_h=0;
            if($row['type']==EasyThumb::SCALE_PROJECTIVE){
                $rate=$width/$height;
                $rate_target=$w/$h;
                if($rate < $rate_target){ //with height
                    $target_height=$h;
                    $target_width=$target_height*$rate;
                }
                else{
                    $target_width=$w;
                    $target_height=$target_width/$rate;
                }
            }
            else if($row['type']==EasyThumb::SCALE_FREE){
                $target_width=$w;
                $target_height=$h;
            }
            $target_x=($w-$target_width)/2;
            $target_y=($h-$target_height)/2;
            $target_gd=$this->createOutFile($w,$h,$row['backgroundcolor']);
            $result=imagecopyresampled($target_gd, $gd, $target_x, $target_y, 0, 0,$target_width,$target_height, $width, $height);
            if(!$result){
                throw new Exception("Scale $file failed");
            }
            if(isset($row['filepath'])){ 
                $this->save($target_gd,$row['filepath']);
            }
            else{
                $this->save($target_gd,$this->defaultName($file,$w,$h));
            }
        }
    }

    private function defaultName($file,$w,$h)
    {
        $info=pathinfo($file);
        return sprintf("%s/%s-%d_%d.%s",$info['dirname'],$info['filename'],$w,$h,$info['extension']);
    }
}
