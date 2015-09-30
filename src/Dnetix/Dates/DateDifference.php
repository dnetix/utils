<?php  namespace Dnetix\Dates;

use DateInterval;

/**
 * Class DateDifference
 * @author Diego Calle
 * @package Dnetix\Dates
 */
class DateDifference {

    const MONTH = 1;
    const DAY = 2;
    const WEEK = 6;
    const DAYSPERWEEK = 7;
    const MONTHSPERYEAR = 12;

    public $dateInterval;
    public $intervals = ['y', 'm', 'd', 'h', 'i', 's'];

    public $nextInterval = true;

    public $translations = [
        'año',
        'mes',
        'día',
        'hora',
        'minuto',
        'segundo',
        'semana'
    ];

    function __construct(DateInterval $dateInterval) {
        $this->dateInterval = $dateInterval;
    }

    public function inDays(){
        return (int) $this->dateInterval->format('%a');
    }

    public function inMonths(){
        return ($this->dateInterval->y * self::MONTHSPERYEAR) + $this->dateInterval->m;
    }

    public function isFuture(){
        return $this->dateInterval->invert ? true : false;
    }

    public function isNow(){
        if(!$this->isToday()){
            return false;
        }
        if($this->dateInterval->h === 0 && $this->dateInterval->i === 0 && $this->dateInterval->s < 2){
            return true;
        }else{
            return false;
        }
    }

    public function isToday(){
        if($this->dateInterval->y === 0 && $this->dateInterval->m === 0 && $this->dateInterval->d === 0){
            return true;
        }else{
            return false;
        }
    }

    public function forHumans(){

        if ($this->isNow()) {
            return "Ahora mismo";
        }

        foreach ($this->intervals as $checkingUnit => $unit) {
            if ($this->{$unit} != 0) {
                $biggerUnit = $checkingUnit;
                break;
            }
        }

        $text = [];
        if($this->isFuture()){
            $text[] = 'en';
        }else{
            $text[] = 'hace';
        }

        // Special case WEEKS
        if($biggerUnit == self::DAY && $this->getValueFromUnit($biggerUnit) > self::DAYSPERWEEK){
            $text[] = $this->getValueFromUnit(self::WEEK).' '.$this->pluralize(self::WEEK);
            // Instead of changing unit just remove the number of days told
            $this->{$this->intervals[self::DAY]} -= $this->getValueFromUnit(self::WEEK) * self::DAYSPERWEEK;
        }else{
            $text[] = $this->getValueFromUnit($biggerUnit).' '.$this->pluralize($biggerUnit);
            $biggerUnit = $this->nextUnit($biggerUnit);
        }

        if($biggerUnit && $this->unitHasValue($biggerUnit)){
            $text[] = 'y '.$this->getValueFromUnit($biggerUnit).' '.$this->pluralize($biggerUnit);
        }

        return implode(' ', $text);

    }

    public function getValueFromUnit($unit){
        if(array_key_exists($unit, $this->intervals)){
            return $this->{$this->intervals[$unit]};
        }else{
            // If key doesn't exists we're talking about weeks
            return floor($this->{$this->intervals[self::DAY]} / self::DAYSPERWEEK);
        }

    }

    public function pluralize($unit){
        if($this->getValueFromUnit($unit) > 1){
            // Special case
            if($unit == self::MONTH){
                return "meses";
            }
            return $this->translations[$unit].'s';
        }else{
            return $this->translations[$unit];
        }

    }

    public function nextUnit($unit){
        if(isset($this->intervals[$unit + 1])){
            return $unit + 1;
        }
        return false;
    }

    public function unitHasValue($unit){
        return ($this->getValueFromUnit($unit) > 0) ? true : false;
    }

    function __get($name){
        if(method_exists($this, $name)){
            return $this->{$name}();
        }
        return $this->dateInterval->{$name};
    }

}