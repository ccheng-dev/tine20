<?php
/**
 * TimeZoneConvert
 *
 * @package     TimeZoneConvert
 * @license     MIT, BSD, and GPL
 * @copyright   Copyright (c) 2012 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Cornelius Weiß <c.weiss@metaways.de>
 */

/**
 * represents a transition rule
 * 
 * NOTE: we only support wkday/numwk for transistion description atm.
 *       The Olson timezone db uses >="day of month", but we
 *       don't need this atm.
 */
class TimeZoneConvert_TransitionRule extends TimeZoneConvert_Model
{
    /**
     * month of transition 1-12
     * @var int
     */
    public $month;
    
    /**
     * week day of transition (0=sunday, ... 6=saturday)
     * @var int
     */
    public $wkday;
    
    /**
     * number of week in month +/- 1-5
     * negative number means: count from end of month
     * @var int
     */
    public $numwk;
    
    /**
     * hour of transition in timezones local time
     * @var int
     */
    public $hour;
    
    /**
     * minute of transition in timezones local time
     * @var int
     */
    public $minute;
    
    /**
     * second of transition in timezones local time
     * @var int
     */
    public $second;
    
    /**
     * transition rule is valid from this date time
     * @var DateTime
     */
    public $from;
    
    /**
     * transistion rule is valid until this date time
     * @var DateTime
     */
    public $until;
    
    /**
     * offset in seconds from UTC this transition results in
     * @var int
     */
    public $offset;
    
    /**
     * transtion results in dst
     * @var bool
     */
    public $isdst;
    
    /**
     * abbriviation of timezone
     * 
     * @var string
     */
    public $abbr;
    
    /**
     * holds defined transistion dates
     * @var array of DateTime
     */
    protected $_transitionDates = array();
    
    /**
     * add a fixed/defined transition date
     * 
     * @param DateTime $dateTime
     */
    public function addTransitionDate(DateTime $dateTime)
    {
        $this->_transitionDates[] = $dateTime;
    }
    
    /**
     * returns clone of defined transition dates
     * 
     * @return array()
     */
    public function getTransitionDates()
    {
        $transitionDates = array();
        foreach($this->_transitionDates as $date) {
            $transitionDates[] = clone $date;
        }
        
        return $transitionDates;
    }
    
    /**
     * returns transition date time for given year
     * 
     * @param  int $year
     * @return DateTime
     */
    public function computeTransition($year)
    {
        $transition = DateTime::createFromFormat('Y-m-d G:i:s', 
            "{$year}-{$this->month}-1 {$this->hour}:{$this->minute}:{$this->second}",
            new DateTimeZone('UTC')
        );
        
        if ($transition == FALSE) {
            throw new Exception('invalid transition rule');
        }
        
        $sign = $this->numwk < 0 ? '-' : '+';
        
        if ($sign == '-') {
            $transition->modify("+1 month -1 day");
        }
        
        while($transition->format('w') != $this->wkday) {
            $transition->modify($sign . '1 day');
        }
        $transition->modify($sign . (abs($this->numwk) -1) . ' weeks');
        
        return $transition;
    }
    
    /**
     * returns TRUE if given rule has an recurring rule
     */
    public function isRecurringRule()
    {
        return ! is_null($this->month);
    }
}