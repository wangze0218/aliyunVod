<?php
namespace James\AliyunVod\Facades;
use Illuminate\Support\Facades\Facade;

class AliyunVod extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'aliuyunVod';
    }
}
