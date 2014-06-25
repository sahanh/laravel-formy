<?php
namespace SH\LaravelFormy;

use Illuminate\Support\Facades\Facade;

class FormyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-formy'; 
    }
}