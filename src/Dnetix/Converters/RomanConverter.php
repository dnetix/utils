<?php

namespace Dnetix\Converters;

class RomanConverter extends Converter
{
    
    private $value;

    protected function __construct($value = null)
    {
        $this->value = $this->clearString($value);
    }

    private function clearString($value)
    {
        return preg_replace("/[\t ]/", '', strtoupper($value));
    }


    public function identifier()
    {
        return 'Romans';
    }

    public function value()
    {
        return $this->value;
    }

    public static function load($value)
    {
        return new self($value);
    }

}