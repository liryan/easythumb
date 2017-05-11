<?php
namespace EasyThumb;

use Exception;
class EasyFile{
    const JPEG=1;
    const JPG=2;
    const GIF=8;
    const PNG=16;
    const BIN=64;

    const SCALE_PROJECTIVE=1;
    const SCALE_FREE=2;

    private $location='';
    private $size=[];
    private $limitsize=0;
    private $limittype=0;
    private $fieldname='';
    private $localfile='';
    private $const_ar;
    public function __construct()
    {
        $this->const_ar=[
            self::JPEG=>['jpg','image/jpeg',function(){
                return JPGThumb();
            }],
            self::JPG=>['jpg','image/jpeg',function(){
                return JPGThumb();
            }],
            self::GIF=>['gif','image/gif',function(){
                return GIFThumb();
            }],
            self::PNG=>['png','image/png',function(){
                return PNGThumb();
            }],
        ];
    }

    public function where($path)
    {
        $this->location=$path;
        return $this;
    }

    public function size($w,$h,$type=Thumb::PROJECTIVE,$backgroundcolor='0x000000')
    {
        $this->size[]=['w'=>$w,'h'=>$h,'type'=>$type,'backgroundcolor'=>$backgroundcolor];
        return $this;
    }

    public function limit($size=0,$type=0)
    {
        $this->limitsize=$size;
        $this->limittype=$type;
    }

    public function upload($fieldname)
    {
        $this->fieldname=$fieldname;
    }

    public function from($path)
    {
        $this->localfile=$path;
    }

    public function done()
    {
        if(!file_exists($this->location)){
            throw new Exception("Directory does'n exist");
        }
        if(!$localfile){
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
            if ($upfile['size'] > $this->sizelimit) {
                throw new RuntimeException('Exceeded filesize limit.');
            }
            
            if($this->limittype!=0 && $this->limittype!=self::$BIN ){
                $checktypes=[];
                foreach($this->const_ar as $flag=>$mime){
                    if($this->limittype & $flag == $flag){
                        $checktypes[$mine[0]]=$mime[1]; 
                    }
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                if (false === $ext = array_search(
                    $finfo->file($upfile['tmp_name']),
                    $checktypes,
                    true
                )) {
                    throw new Exception('Invalid file format.');
                }
            }

            $filename=sha1($upfile['tmp_name']).".".$ext;
            $this->localfile=dirname($this->location)."/".$filename;
            if(!move_uploaded_file($upfile['tmp_name'],$this->localfile)){
                throw new Exception("Failed file to $this->location"); 
            }
        }

        if(!file_exists($this->localfile)){
            throw new Exception("File dosen't exist: $this->location"); 
        }  

        foreach($this->const_ar as $row){
            if($row[0]==$ext && $this->size){
                return $row[2]()->toSize($this->localfile,$this->size);
            }
        }
        return true;
    }
}
