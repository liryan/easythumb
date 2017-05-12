<?php
/**
 * EasyThumb 
 * 
 * @uses Facade
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan <canbetter@qq.com> 
 * @license MIT {@link http://ryanli.net}
 */
namespace EasyThumb;
use Illuminate\Support\Facades\Facade;

class EasyThumb extends Facade
{
    const SCALE_PROJECTIVE=1;
    const SCALE_FREE=2;

    const JPEG=1;
    const JPG=2;
    const GIF=8;
    const PNG=16;
    const BIN=64;

    protected static function getFacadeAccessor()
    {
        return 'easythumb';
    }
}
