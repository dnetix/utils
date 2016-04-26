<?php  namespace Dnetix\Dates;

use DateInterval;
use DateTime;
use Exception;

/**
 * Class DateHelper
 * A utility for the management of dates in PHP, works like Carbon in Spanish
 *
 * @property      integer $year
 * @property      integer $month
 * @property      integer $day
 * @property      integer $hour
 * @property      integer $minute
 * @property      integer $second
 * @property      integer $timestamp seconds since the Unix Epoch
 * @property-read integer $micro
 * @property-read integer $dayOfWeek 0 (for Sunday) through 6 (for Saturday)
 * @property-read integer $dayOfYear 0 through 365
 * @property-read integer $weekOfMonth 1 through 6
 *
 * @author Diego Calle
 * @package Dnetix\Dates
 */
class DateHelper extends DateTime
{

    /**
     * The day constants
     */
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    /**
     * Format constants
     */
    public static $FORMATS = [
        'year' => 'Y',
        'month' => 'n',
        'day' => 'j',
        'hour' => 'G',
        'minute' => 'i',
        'second' => 's',
        'micro' => 'u',
        'dayOfWeek' => 'w',
        'dayOfYear' => 'z',
        'weekOfYear' => 'W',
        'daysInMonth' => 't',
        'timestamp' => 'U',
    ];

    /**
     * Names of days of the week.
     * @var array
     */
    protected static $DAYS = [
        self::SUNDAY => 'Domingo',
        self::MONDAY => 'Lunes',
        self::TUESDAY => 'Martes',
        self::WEDNESDAY => 'Miercoles',
        self::THURSDAY => 'Jueves',
        self::FRIDAY => 'Viernes',
        self::SATURDAY => 'Sabado'
    ];

    protected static $DAYS_SUFFIX = [
        'L' => self::MONDAY,
        'M' => self::TUESDAY,
        'W' => self::WEDNESDAY,
        'J' => self::THURSDAY,
        'V' => self::FRIDAY,
        'S' => self::SATURDAY,
        'D' => self::SUNDAY
    ];

    /**
     * Translation for the months of the year
     * @var array
     */
    protected static $MONTHS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function __construct($date = 'now', $timeZone = null)
    {
        parent::__construct($date, $timeZone);
    }

    /**
     * Creates a new instance of the class, if no date provided it comes with the actual moment.
     * @param string $date
     * @param null $timeZone
     * @return DateHelper
     */
    public static function create($date = 'now', $timeZone = null)
    {
        return new self($date, $timeZone);
    }

    /**
     * Returns a DateDifference between the date provided and the toDate, if not provided the actual moment
     * @param $fromDate
     * @param string $toDate
     * @return DateDifference
     */
    public static function getDifference($fromDate, $toDate = 'now')
    {
        return self::create($fromDate)->toDifferenceWith($toDate);
    }

    /**
     * Returns the DateDifference between the time of the instance and the date provided or the actual moment if not.
     * @param string $toDate
     * @return DateDifference
     */
    public function toDifferenceWith($toDate = 'now')
    {
        return new DateDifference($this->diff(new self($toDate)));
    }

    /**
     * Allows to access the private properties of the DateTime formatted
     * @param $name
     * @return string
     * @throws Exception
     */
    public function __get($name)
    {
        if (array_key_exists($name, self::$FORMATS)) {
            return $this->format(self::$FORMATS[$name]);
        }
        throw new Exception("The format cant be parsed");
    }

    public function getDayName()
    {
        return self::$DAYS[$this->dayOfWeek];
    }

    public function getMonthName()
    {
        return self::$MONTHS[$this->month - 1];
    }

    public function getYear()
    {
        return $this->year;
    }

    /**
     * Modifies the date with the interval specification
     * P#Y#M#D#WT#H#M#S
     * @param $intervalSpec
     * @param bool $add
     * @return $this
     */
    public function interval($intervalSpec, $add = true)
    {
        $interval = new DateInterval($intervalSpec);
        if($add) {
            $this->add($interval);
        }else{
            $this->sub($interval);
        }
        return $this;
    }

    public function addYears($years)
    {
        $this->interval('P' . abs($years) . 'Y', $years > 0);
    }

    public function addMonths($months)
    {
        $this->interval('P' . abs($months) . 'M', $months > 0);
    }

    public function addDays($days)
    {
        $this->interval('P' . abs($days) . 'D', $days > 0);
    }

    /* Some templates for the format */

    /**
     * Returns hh:mm AM/PM the hour and minutes with the meridian
     * @return string
     */
    public function getTimeMeridian()
    {
        return $this->format('h:i A');
    }

    /**
     * Returns HH:mm
     * @return string
     */
    public function getTime()
    {
        return $this->format('H:i');
    }

    /**
     * Formats the date according in MySQL format
     * @return string
     */
    public function getSQLDate()
    {
        return $this->format('Y-m-d');
    }

    /**
     * Returns the date time formatted as MySQL timestamp
     * @return string
     */
    public function getSQLTimestamp()
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function changeTime($time)
    {
        list($hour, $minutes) = explode(':', $time);
        $this->setTime($hour, $minutes);
    }

    /**
     * Returns the number of the week
     * @return string
     */
    public function getWeekNumber()
    {
        return $this->format('W');
    }

    public static function parseSuffix($suffix)
    {
        return isset(self::$DAYS_SUFFIX[$suffix]) ? self::$DAYS_SUFFIX[$suffix] : null;
    }
}
