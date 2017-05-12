<?php
namespace EasyThumb;
use EasyThumb\Genimg\Thumb;
use Exception;
class EasyFile
{
    private $location='';
    private $size=[];
    private $limitsize=0;
    private $limittype=0;
    private $fieldname='';
    private $localfile='';

    public function where($path)
    {
        $this->location=$path;
        return $this;
    }

    public function size($w,$h,$type=EasyThumb::SCALE_PROJECTIVE,$backgroundcolor='0x000000')
    {
        $this->size[]=['w'=>$w,'h'=>$h,'type'=>$type,'backgroundcolor'=>$backgroundcolor];
        return $this;
    }

    public function limit($size=0,$type=0)
    {
        $this->limitsize=$size;
        $this->limittype=$type;
        return $this;
    }

    public function upload($fieldname)
    {
        $this->fieldname=$fieldname;
        return $this;
    }

    public function from($path)
    {
        $this->localfile=$path;
        return $this;
    }

    public function done()
    {
        if(!file_exists($this->location)){
            throw new Exception("Directory does'n exist[$this->location]");
        }
        if(!$this->localfile){
            if(!$this->fieldname || !isset($_FILES[$this->fieldname]) ){
                throw new Exception("No file to process");
            }
            $upfile=$_FILES[$this->fieldname];
            switch ($upfile['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception('Exceeded filesize limit.');
                default:
                    throw new Exception('Unknown errors.');
            }
            if ($upfile['size'] > $this->limitsize && $this->limitsize>0) {
                throw new Exception('Exceeded filesize limit.');
            }

            $thumb=Thumb::checkType($upfile['tmp_name'],$this->limittype);
            if(!$thumb){
                throw new Exception("File type is not allow");
            }
    
            $filename=sha1(rand(1000,9999).$upfile['tmp_name']).".".$thumb->extension();

            $this->localfile=(is_dir($this->location)?$this->location:dirname($this->location))."/".$filename;
            if(!move_uploaded_file($upfile['tmp_name'],$this->localfile)){
                throw new Exception("Failed file to $this->location"); 
            }
        }
        else{
            $thumb=Thumb::checkType($this->localfile);
        }

        if(!file_exists($this->localfile)){
            throw new Exception("File dosen't exist: $this->location"); 
        }  
        
        $thumb->toSize($this->localfile,$this->size);
        return $this->localfile;;
    }
}
