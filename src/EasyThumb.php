<?php
namespace EasyThumb;
use Illuminate\Support\Facades\Facade;

class EasyThumb extends Facade
{
<<<<<<< HEAD
    const SCALE_PROJECTIVE=1;
    const SCALE_FREE=2;

=======
>>>>>>> aa90a4f49c335634aedfac192ed05c38c63ccd1b
    const JPEG=1;
    const JPG=2;
    const GIF=8;
    const PNG=16;
    const BIN=64;

<<<<<<< HEAD

=======
    const SCALE_PROJECTIVE=1;
    const SCALE_FREE=2;
>>>>>>> aa90a4f49c335634aedfac192ed05c38c63ccd1b
    protected static function getFacadeAccessor()
    {
        return 'easythumb';
    }
}
