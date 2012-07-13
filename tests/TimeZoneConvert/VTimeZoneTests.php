<?php
/**
 * TimeZoneConvert
 *
 * @package     TimeZoneConvert
 * @subpackage  Tests
 * @license     MIT, BSD, and GPL
 * @copyright   Copyright (c) 2012 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Cornelius Weiß <c.weiss@metaways.de>
 */

class TimeZoneConvert_VTimeZoneTests extends PHPUnit_Framework_TestCase
{
    
    public static $rfc5545AmericaNewYork = <<<EOT
BEGIN:VTIMEZONE
TZID:America/New_York
LAST-MODIFIED:20050809T050000Z
BEGIN:DAYLIGHT
DTSTART:19670430T020000
RRULE:FREQ=YEARLY;BYMONTH=4;BYDAY=-1SU;UNTIL=19730429T070000Z
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19671029T020000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU;UNTIL=20061029T060000Z
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
TZNAME:EST
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19740106T020000
RDATE:19750223T020000
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
END:DAYLIGHT
BEGIN:DAYLIGHT
DTSTART:19760425T020000
RRULE:FREQ=YEARLY;BYMONTH=4;BYDAY=-1SU;UNTIL=19860427T070000Z
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
END:DAYLIGHT
BEGIN:DAYLIGHT
DTSTART:19870405T020000
RRULE:FREQ=YEARLY;BYMONTH=4;BYDAY=1SU;UNTIL=20060402T070000Z
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
END:DAYLIGHT
BEGIN:DAYLIGHT
DTSTART:20070311T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:20071104T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
TZNAME:EST
END:STANDARD
END:VTIMEZONE
EOT;
    
    /**
     * https://bugzilla.mozilla.org/show_bug.cgi?id=504299
     */
    public static $customAsiaJerusalem = <<<EOT
BEGIN:VTIMEZONE
TZID:Asia/Jerusalem
X-LIC-LOCATION:Asia/Jerusalem
BEGIN:STANDARD
TZOFFSETFROM:+0300
TZOFFSETTO:+0200
TZNAME:IST
DTSTART:20081005T020000
RDATE;VALUE=DATE-TIME:20081005T020000,20090927T020000,20100927T020000,20111002T020000,
20120923T020000,20130908T020000,20140928T020000,20150920T020000,20161009T020000,
20170924T020000,20180916T020000,20191006T020000,20200927T020000,20210912T020000,
20221002T020000,20230924T020000,20241006T020000,20250928T020000,20260920T020000,
20271010T020000,20280924T020000,20290916T020000,20301006T020000,20310921T020000,
20320912T020000,20331002T020000,20340917T020000,20351007T020000,20360928T020000,
20370913T020000
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:20090327T020000
RDATE;VALUE=DATE-TIME:20090327T020000,20100326T020000,20110401T020000,20120330T020000,
20130329T020000,20140328T020000,20150327T020000,20160401T020000,20170331T020000,
20180330T020000,20190329T020000,20200327T020000,20210326T020000,20220401T020000,
20230331T020000,20240329T020000,20250328T020000,20260327T020000,20270326T020000,
20280331T020000,20290330T020000,20300329T020000,20310328T020000,20320326T020000,
20330401T020000,20340331T020000,20350330T020000,20360328T020000,20370327T020000
TZOFFSETFROM:+0200
TZOFFSETTO:+0300
TZNAME:Jerusalem DST
END:DAYLIGHT
END:VTIMEZONE
EOT;
    
    /**
     * @var TimeZoneConvert_VTimeZone
     */
    public $uit;
    
    public function setUp()
    {
        $this->uit = new TimeZoneConvert_VTimeZone();
    }
    
//     public function testRrule2Transition()
//     {
//         // Europe/Berlin Daylight
//         $transition = $this->uit->rrule2Transition("FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU", "19810329T020000", '2012');
//         $this->assertEquals('2012-03-25 02:00:00', $transition->format('Y-m-d H:i:s'));
//     }
    
    public function testGetDateTimeIdentifier()
    {
        $this->uit->getDateTimeIdentifier(self::$rfc5545AmericaNewYork);
    }
}