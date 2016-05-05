<?php

namespace Dnetix\Converters;


class DecimalConverter extends Converter
{

    private $value;
    
    protected function __construct($value = null)
    {
        if(!is_numeric($value)){
            throw new \Exception("The value to convert has to be numeric");
        }
        $this->value = (int) $value;
    }
    
    /**
     * Returns the value in the converter to convert
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Returns the identifier for the converter
     * @return string
     */
    public function identifier()
    {
        return 'decimal';
    }
    
    public static function load($value)
    {
        return new self($value);
    }
}