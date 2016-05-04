<?php

namespace Dnetix\Dates;

use DateTime;
use Exception;

/**
 * Provides a simple string format to check if a given datetime its in that range
 * L = monday, M = tuesday, W = wednesday, J = thursday, V = friday, S = saturday, D = sunday
 * For example:
 *  LV8:05-16:20|S6-14|!S8-9
 * Will provide a check true from monday to friday between 8:05 to 16:20, saturday from 6 to 8 and 9 to 14
 * because exclamation sign negates the range saturday from 8 to 9
 * As you can see the bar '|' separates different conditions
 * If three or more day letters are provided then its not a range, those are separate days
 *  LMJS8-12
 * Will provide a check true for monday, tuesday, thursday and saturday between 8 to 12, note that wednesday
 * and friday are not included.
 */
class DateRangeChecker
{

    private $regexExpression = "/(!)?([A-z]+)(\d+)?:?(\d+)?-?(\d+)?:?(\d+)?/";

    const IX_EXPRESSION = 0;
    const IX_NEGATION = 1;
    const IX_DAYS = 2;
    const IX_INITIAL_HOUR = 3;
    const IX_INITIAL_MINS = 4;
    const IX_FINAL_HOUR = 5;
    const IX_FINAL_MINS = 6;

    protected $invalidRanges = [];
    protected $validRanges = [];

    private $holidayCheck = false;
    private $allowAll = false;

    public function __construct($rangeString)
    {
        // Creates an array for the regex expression
        preg_match_all($this->regexExpression, $rangeString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $this->readMatch($match);
        }
    }

    private function readMatch($match)
    {
        // Define if the given match should allow or deny the time window provided
        $allow = $match[self::IX_NEGATION] != '!';

        // By default it creates the whole day window
        $timeRange = [0, 2359];

        $expressionDays = $match[self::IX_DAYS];
        if ($expressionDays == 'H') {
            // Its Holidays
            $this->holidayCheck = true;
            $this->addCondition(['H'], $allow, $timeRange);
            return true;
        } elseif ($expressionDays == 'A') {
            $this->allowAll = true;
            return true;
        }

        $days = $this->expressionToDays($expressionDays);
        $timeRange = $this->parseTimeRange($match);

        $this->addCondition($days, $allow, $timeRange);
    }

    /**
     * Converts a days expression to the actual DateTime numbered days array
     * @param $expression
     * @return array
     * @throws Exception
     */
    private function expressionToDays($expression){
        $days = [];
        $expression = str_split($expression);

        if(sizeof($expression) == 1){
            if(($day = DateHelper::parseSuffix($expression[0])) !== null){
                $days[] = $day;
            }else{
                throw new Exception("Invalid Format");
            }
        }elseif(sizeof($expression) == 2){
            $startDay = DateHelper::parseSuffix($expression[0]);
            $endDay = DateHelper::parseSuffix($expression[1]);
            if($startDay !== null && $endDay !== null){
                for($i = $startDay; $i <= $endDay; $i++){
                    $days[] = $i;
                }
            }else{
                throw new Exception("Invalid Format");
            }
        }else{
            // They are days non sequential
            $days = array_map(function($day){
                if($day = DateHelper::parseSuffix($day)){
                    return $day;
                }
            }, $expression);
        }
        return $days;
    }

    /**
     * Returns an array with the initial time and the final time window for the parsed match
     * @param $match
     * @return array
     */
    private function parseTimeRange($match)
    {
        $startTime = isset($match[self::IX_INITIAL_HOUR]) ? $match[self::IX_INITIAL_HOUR] : 0;
        $startTime .= isset($match[self::IX_INITIAL_MINS]) && $match[self::IX_INITIAL_MINS] != '' ? $match[self::IX_INITIAL_MINS] : '00';
        
        $endTime = isset($match[self::IX_FINAL_HOUR]) ? $match[self::IX_FINAL_HOUR] : 24;
        $endTime .= isset($match[self::IX_FINAL_MINS]) && $match[self::IX_FINAL_MINS] != '' ? $match[self::IX_FINAL_MINS] : '00';
        
        return [(int) $startTime, (int) $endTime];
    }

    private function addCondition($days, $allow, $condition){
        if($allow){
            $range =& $this->validRanges;
        }else{
            $range =& $this->invalidRanges;
        }
        
        foreach ($days as $day){
            if(isset($range[$day])){
                $range[$day][] = $condition;
            }else{
                $range[$day] = [$condition];
            }
        }
    }

    public function check($datetime = null)
    {
        if(is_null($datetime)) {
            $datetime = new DateTime();
        }elseif(!($datetime instanceof DateTime)){
            $datetime = $this->createDatetime($datetime);
        }
        
        $dayOfWeek = $datetime->format('w');
        $time = (int) $datetime->format('Gi');
        
        $invalidRangesDay = isset($this->invalidRanges[$dayOfWeek]) ? $this->invalidRanges[$dayOfWeek] : null;
        if($invalidRangesDay && $this->checkForRanges($invalidRangesDay, $time)){
            return false;
        }

        if($this->allowAll){
            return true;
        }

        $validRangesDay = isset($this->validRanges[$dayOfWeek]) ? $this->validRanges[$dayOfWeek] : null;
        if($validRangesDay){
            // First im checking if there is a negated range for this time
            if($this->checkForRanges($validRangesDay, $time)){
                return true;
            }
        }

        return false;
    }
    
    private function checkForRanges($ranges, $time){
        foreach ($ranges as $range){
            if($range[0] <= $time && $time <= $range[1]){
                return true;
            }
        }
        return false;
    }

    private function createDatetime($time)
    {
        $date = new DateTime();
        return $date->setTimestamp($time);
    }

    public static function load($rangeString)
    {
        return new self($rangeString);
    }
}
