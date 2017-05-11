<?php
namespace EasyThumb;
use Illuminate\Support\Facades\Facade;

class EasyThumb extends Facade
{
    const JPEG=1;
    const JPG=2;
    const GIF=8;
    const PNG=16;
    const BIN=64;

    const SCALE_PROJECTIVE=1;
    const SCALE_FREE=2;
    protected static function getFacadeAccessor()
    {
        return 'easythumb';
    }
}
