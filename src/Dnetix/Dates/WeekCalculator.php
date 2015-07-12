<?php  namespace Dnetix\Dates;

use DateTime;

/**
 * Class WeekCalculator
 * @author Diego Calle
 * @package Dnetix\Dates
 */
class WeekCalculator {

    /**
     * @var DateTime
     */
    private $fromDay;

    private $startWeekDate;
    private $endWeekDate;
    private $weekNumber;

    private $format = 'Y-m-d';

    private function __construct($from){
        $this->fromDay = new DateTime($from);
        $this->calculateWeekFromDay();
    }

    private function calculateWeekFromDay(){
        $this->weekNumber = $this->fromDay->format('W');

        $dayOfWeek = $this->fromDay->format('w');
        if($dayOfWeek == '0'){
            $this->endWeekDate = $this->fromDay;
        }else{
            $this->endWeekDate = $this->fromDay->add(new \DateInterval('P'.(7 - $dayOfWeek).'D'));
        }
        $this->startWeekDate = clone($this->endWeekDate);
        $this->startWeekDate->sub(new \DateInterval('P6D'));
    }

    public static function fromDay($day = 'now'){
        return new self($day);
    }

    public static function fromWeekNumber($weekNumber, $year = null){
        if(is_null($year)){
            $year = date('Y');
        }
        $fromDay = new DateTime($year.'-01-01');
        $add = 0;
        if($fromDay->format('W') != '1'){
            $add = 6;
        }
        $fromDay->add(new \DateInterval('P'.((7 * ($weekNumber - 1)) + $add).'D'));
        return new self($fromDay->format('Y-m-d'));
    }

    public function weekNumber() {
        return (int) $this->weekNumber;
    }

    public function weekYear() {
        return (int) $this->fromDay->format('Y');
    }

    public function lastWeekNumber(){
        if($this->weekNumber() == 1){
            $lastWeekCalculator = new self($this->startLastWeekDate());
            return $lastWeekCalculator->weekNumber();
        }else{
            return $this->weekNumber() - 1;
        }
    }

    public function nextWeekNumber(){
        if($this->weekNumber() >= 52){
            $nextWeekCalculator = new self($this->startNextWeekDate());
            return $nextWeekCalculator->weekNumber();
        }else{
            return $this->weekNumber() + 1;
        }
    }

    public function startWeekDate(){
        return $this->startWeekDate->format($this->format);
    }

    public function endWeekDate(){
        return $this->endWeekDate->format($this->format);
    }

    public function startLastWeekDate(){
        $lastWeek = clone($this->startWeekDate);
        return $lastWeek->sub(new \DateInterval('P7D'))->format($this->format);
    }

    public function endLastWeekDate(){
        $lastWeek = clone($this->endWeekDate);
        return $lastWeek->sub(new \DateInterval('P7D'))->format($this->format);
    }

    public function startNextWeekDate(){
        $nextweek = clone($this->startWeekDate);
        return $nextweek->add(new \DateInterval('P7D'))->format($this->format);
    }

    public function endNextWeekDate(){
        $nextweek = clone($this->endWeekDate);
        return $nextweek->add(new \DateInterval('P7D'))->format($this->format);
    }

    public function lastWeek(){
        $lastWeek = clone($this->fromDay);
        $lastWeek->sub(new \DateInterval('P7D'));
        return new self($lastWeek->format('Y-m-d'));
    }

    public function fullWeek() {
        return $this->weekNumber().'-'.$this->weekYear();
    }

}