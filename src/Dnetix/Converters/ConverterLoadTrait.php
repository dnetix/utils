<?php


namespace Dnetix\Converters;


trait ConverterLoadTrait
{

    public static function load($value){
        return new self($value);
    }

}