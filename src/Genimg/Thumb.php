<?php
namespace EasyThumb\Genimg;
use EasyThumb\EasyFile;
abstract class Thumb{
    abstract public function createFromFile($file);
    abstract public function save($file);

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
        list($width, $height, $type, $attr)=getimagesize($file);
        if($width == 0 || $height==0){
            throw new Exception("File size is error");
        }
        $gd=$this->createFromFile($file);
        foreach($size as $row){
            $w=$row['w'];
            $h=$row['h'];
            $target_w=0;
            $target_h=0;
            if($row['type']==EasyFile::SCALE_PROJECTIVE){
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
            else if($row['type']==self::SCALE_FREE){
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
                $this->save($row['filepath']);
            }
            else{
                $this->save($this->defaultName($file,$w,$h));
            }
        }
    }

    private function defaultName($file,$w,$h)
    {
        $info=pathinfo($file);
        return sprintf("%s/%s-%d_%d.$%s",$info['dirname'],$info['basename'],$w,$h,$info['extension']);
    }
}
