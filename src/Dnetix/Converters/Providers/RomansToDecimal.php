<?php

namespace Dnetix\Converters\Providers;

class RomansToDecimal implements Provider
{
    protected $equivalents;
    
    public static $ROMAN_DECIMAL = [
        'I' => 1,
        'V' => 5,
        'X' => 10,
        'L' => 50,
        'C' => 100,
        'D' => 500,
        'M' => 1000
    ];

    public function convertValue($value)
    {
        $this->equivalents = array_map(function($roman){
            return self::parseRoman($roman);
        }, str_split($value));
        
        $position = 0;
        $decimal = 0;
        while ($position < $this->endOfEquivalents()){
            $actual = $this->valueAtPosition($position);
            $next = $this->valueAtPosition($position + 1);
            
            if($actual >= $next){
                $decimal += $actual;
                $position++;
            }else{
                $decimal += ($next - $actual);
                $position += 2;
            }
        }
        return $decimal;
    }

    private function endOfEquivalents()
    {
        return sizeof($this->equivalents);
    }

    public function valueAtPosition($position)
    {
        return isset($this->equivalents[$position]) ? $this->equivalents[$position] : null;
    }

    public static function parseRoman($roman)
    {
        if(!isset(self::$ROMAN_DECIMAL[$roman])){
            throw new \Exception("There is no roman equivalent to: " . $roman);
        }
        return self::$ROMAN_DECIMAL[$roman];
    }
}