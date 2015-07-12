<?php  namespace Dnetix\Barcodes;

/**
 * Class Code128
 *
 * Class for the creation of strings for the generation of Code128 barcodes
 *
 * @author Diego Calle
 * @package Dnetix\Barcodes
 */
class Code128 {

    private $string;
    private $checksum;
    private $code128;

    const DIFF = 32;
    const BIGDIFF = 100;
    const STARTB = 204;
    const STOP = 206;
    const CHECKMOD = 103;

    /**
     * Obtains the string parsed to be used with the Code128 font
     * @param $string
     * @return string
     */
    public static function get($string){
        return (new self())->getBarCode($string);
    }

    /**
     * Calculates the string to create the one coded to use with Code128
     * @param $string
     * @return string
     */
    public function getBarCode($string){
        $this->checksum = 104;
        $this->string = $string;
        $string = str_split($string);
        $len = sizeof($string);
        $i = 0;
        $this->code128 = chr(self::STARTB);
        while($i < $len){
            $this->checksum += (ord($string[$i]) - self::DIFF) * ($i + 1);
            $this->code128 .= $string[$i];
            $i++;
        }
        $this->checksum = $this->checksum % self::CHECKMOD;
        if($this->checksum > 94){
            $this->code128 .= chr($this->checksum + self::BIGDIFF);
        }else{
            $this->code128 .= chr($this->checksum + self::DIFF);
        }
        $this->code128 .= chr(self::STOP);
        return utf8_encode($this->code128);
    }

    public function getString(){
        return $this->string;
    }

    public function getChecksum(){
        return $this->checksum;
    }
}