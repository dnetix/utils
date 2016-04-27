<?php

use Dnetix\Dates\DateHelper;
use Dnetix\Dates\DateRangeChecker;

class DateCheckerTest extends TestCase
{

    /**
     * @param $day
     * @param $time
     * @return DateHelper
     */
    public function createDateTime($day, $time)
    {
        $date = DateHelper::create();
        $dayOfWeek = $date->dayOfWeek;
        $dayOfWeekNeeded = DateHelper::parseSuffix($day);
        
        $difference = $dayOfWeekNeeded - $dayOfWeek;
        
        $date->addDays($difference);
        $date->changeTime($time);
        
        return $date;
    }

    public function testIfNoDateTimeProvidedToCheckItCreatesADatetimeNow()
    {
        $this->assertTrue(DateRangeChecker::load('A')->check(), 'It accepts any day or time');
    }

    public function testItParsesTheRangeStringAndValidatesIfTheGivenDateItsInRange()
    {
        $testingDates = [
            ['date' => $this->createDateTime('W', '8:00'), 'expect' => true],
            ['date' => $this->createDateTime('L', '8:00')->getTimestamp(), 'expect' => true],
            ['date' => $this->createDateTime('D', '10:00'), 'expect' => false],
            ['date' => $this->createDateTime('L', '12:31'), 'expect' => false]
        ];

        $checker = DateRangeChecker::load("LV8-12:30|LV14:10-18|S8-12|!H");

        foreach ($testingDates as $testingDate) {
            $this->assertEquals($checker->check($testingDate['date']), $testingDate['expect']);
        }
    }

    public function testParsesThreeDaysAsIndividualDays()
    {
        $testingDates = [
            ['date' => $this->createDateTime('W', '8:00'), 'expect' => false],
            ['date' => $this->createDateTime('L', '8:00')->getTimestamp(), 'expect' => true],
            ['date' => $this->createDateTime('D', '10:00'), 'expect' => false],
            ['date' => $this->createDateTime('L', '12:31'), 'expect' => false],
            ['date' => $this->createDateTime('J', '9:00'), 'expect' => false]
        ];

        $checker = DateRangeChecker::load("LMV8-12:30");

        foreach ($testingDates as $testingDate) {
            $this->assertEquals($checker->check($testingDate['date']), $testingDate['expect']);
        }
    }

    public function testParserIgnoresBadDaysSuffixWhenItsProvidedWithOther2()
    {
        $testingDates = [
            ['date' => $this->createDateTime('W', '8:00'), 'expect' => false],
            ['date' => $this->createDateTime('L', '8:00')->getTimestamp(), 'expect' => true],
            ['date' => $this->createDateTime('D', '10:00'), 'expect' => false],
            ['date' => $this->createDateTime('L', '12:31'), 'expect' => false],
            ['date' => $this->createDateTime('J', '9:00'), 'expect' => false]
        ];

        $checker = DateRangeChecker::load("LMVAZ8-12:30");

        foreach ($testingDates as $testingDate) {
            $this->assertEquals($checker->check($testingDate['date']), $testingDate['expect']);
        }
    }

    public function testAllowsAllButSomeDays()
    {
        $testingDates = [
            ['date' => $this->createDateTime('W', '8:00'), 'expect' => true],
            ['date' => $this->createDateTime('L', '8:00')->getTimestamp(), 'expect' => true],
            ['date' => $this->createDateTime('D', '10:00'), 'expect' => true],
            ['date' => $this->createDateTime('L', '12:31'), 'expect' => true],
            ['date' => $this->createDateTime('M', '12:30'), 'expect' => false],
            ['date' => $this->createDateTime('M', '14:00'), 'expect' => false]
        ];

        $checker = DateRangeChecker::load("A|!M12-14");

        foreach ($testingDates as $testingDate) {
            $this->assertEquals($checker->check($testingDate['date']), $testingDate['expect']);
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testSingleLetterInvalidThrowsException()
    {
        $checker = DateRangeChecker::load("Z");
    }
    
    /**
     * @expectedException \Exception
     */
    public function testInvalidRangeThrowsException()
    {
        $checker = DateRangeChecker::load("LZ10-12");
    }

    public function testItParsesCorrectlyTheFinalTime()
    {
        $testingDates = [
            ['date' => $this->createDateTime('L', '00:10'), 'expect' => true],
            ['date' => $this->createDateTime('L', '1:10')->getTimestamp(), 'expect' => false],
            ['date' => $this->createDateTime('L', '1:01')->getTimestamp(), 'expect' => false],
        ];

        $checker = DateRangeChecker::load("L0-1");

        foreach ($testingDates as $testingDate) {
            $this->assertEquals($checker->check($testingDate['date']), $testingDate['expect']);
        }
    }
}
