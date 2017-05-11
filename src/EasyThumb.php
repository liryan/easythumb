<?php
namespace EasyThumb;
use Illuminate\Support\Facades\Facade;

class EasyThumb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'easythumb';
    }
}
