<?php
/**
 * EasyFile 
 * 
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan <canbetter@qq.com> 
 * @license MIT {@link http://ryanli.net}
 */

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

    /**
     * where 
     * 要存放文件的路径
     * @param mixed $path 
     * @access public
     * @return void
     */
    public function where($path)
    {
        $this->location=$path;
        return $this;
    }

    /**
     * size 
     * 尺寸缩放
     * @param mixed $w  宽
     * @param mixed $h  高
     * @param mixed $type  缩放类型
     * @param string $backgroundcolor  背景颜色填充
     * @param string $filepath  该尺寸的文件路径
     * @access public
     * @return void
     */
    public function size($w,$h,$type=EasyThumb::SCALE_PROJECTIVE,$backgroundcolor='0x000000',$filepath='')
    {
        $this->size[]=['w'=>$w,'h'=>$h,'type'=>$type,'backgroundcolor'=>$backgroundcolor,'filepath'=>$filepath];
        return $this;
    }

    /**
     * limit 
     * 文件的大小，类型限制
     * @param int $size 
     * @param int $type 
     * @access public
     * @return void
     */
    public function limit($size=0,$type=0)
    {
        $this->limitsize=$size;
        $this->limittype=$type;
        return $this;
    }

    /**
     * upload 
     * 处理上传 
     * @param mixed $fieldname  上传表单的文件input的name
     * @access public
     * @return void
     */
    public function upload($fieldname)
    {
        $this->fieldname=$fieldname;
        return $this;
    }

    /**
     * from 
     * 处理本地文件
     * @param mixed $path 
     * @access public
     * @return void
     */
    public function from($path)
    {
        $this->localfile=$path;
        return $this;
    }

    /**
     * done 
     * 执行处理逻辑
     * @access public
     * @return void
     */
    public function done()
    {
        if(is_file($this->location)){
            $this->location=dirname($this->location);
        }

        if(is_dir($this->location) && !file_exists($this->location)){
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
        
        $result=[
            'origin'=>$this->localfile,
        ];

        $result['files']=$thumb->toSize($this->localfile,$this->size);
        return $result;
    }
}
