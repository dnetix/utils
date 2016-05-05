<?php

namespace Dnetix\Converters\Providers;

class DecimalToRomans implements Provider
{
    public static $DECIMAL_ROMAN = [
        ['M', 1000],
        ['CM', 900],
        ['D', 500],
        ['CD', 400],
        ['C', 100],
        ['L', 50],
        ['X', 10],
        ['IX', 9],
        ['V', 5],
        ['IV', 4],
        ['I', 1],
    ];
    
    private $actual = 0;

    public function convertValue($value)
    {
        $romans = [];
        list($roman, $decimal) = $this->nextEquivalent();
        while($value > 0){

            if($value >= $decimal){
                $romans[] = $roman;
                $value -= $decimal;
            }else{
                list($roman, $decimal) = $this->nextEquivalent();
            }
        }
        
        return implode('', $romans);
    }

    private function nextEquivalent()
    {
        if($this->actual < sizeof(self::$DECIMAL_ROMAN)){
            $equivalent = self::$DECIMAL_ROMAN[$this->actual];
            $this->actual++;
            return $equivalent;
        }
    }
}