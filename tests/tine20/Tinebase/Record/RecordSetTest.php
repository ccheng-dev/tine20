<?php
/**
 * Tine 2.0 - http://www.tine20.org
 * 
 * @package     Tinebase
 * @subpackage  Record
 * @license     http://www.gnu.org/licenses/agpl.html
 * @copyright   Copyright (c) 2007-2008 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Cornelius Weiss <c.weiss@metaways.de>
 * @version     $Id$
 */
/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';
// Call Tinebase_Record_RecordSetTest::main() if this source file is executed directly.
if (! defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Tinebase_Record_RecordSetTest::main');
}
/**
 * Test class for Tinebase_Record_RecordSet.
 * Generated by PHPUnit on 2008-02-15 at 09:37:50.
 */
class Tinebase_Record_RecordSetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tinebase_Record_RecordSet
     */
    protected $object;
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main ()
    {
        $suite = new PHPUnit_Framework_TestSuite('Tinebase_Record_RecordSetTest');
        PHPUnit_TextUI_TestRunner::run($suite);
    }
    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp ()
    {
        $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('string' => 'idLess1'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('string' => 'idLess2'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => 1, 'string' => 'idFull1'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => 2, 'string' => 'idFull2'), true));
    }
    /**
     * Tears down the fixture.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown ()
    {}
    
    public function testCloneRecordSet()
    {
        $obj = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $obj->addRecord(new Tinebase_Record_DummyRecord(array('date_single' => Zend_Date::now()), true));
        
        $clone = clone $obj;
        
        $this->assertFalse($obj->getFirstRecord() === $clone->getFirstRecord(), 'recordSet is not cloned');
        $this->assertFalse($obj->getFirstRecord()->date_single === $clone->getFirstRecord()->date_single, 'member var of record is not cloned');
    }
    
    public function testFind()
    {
        $toFind = new Tinebase_Record_DummyRecord(array('string' => 'toFind'), true);
        $this->object->addRecord($toFind);
        
        $found = $this->object->find('string', 'toFind');
        $this->assertTrue($toFind === $found);
    }
    
    public function testRemoveRecord()
    {
        $idLess1 = $this->object->find('string', 'idLess1');
        $this->object->removeRecord($idLess1);
        
        foreach($this->object as $record) {
            $this->assertFalse($record === $idLess1, 'idLess1 is still in set');
        }
    }
    
    /**
     * Tests exception if wrong record type given in the initial data array
     */
    public function testConstructorWrongRecord ()
    {
        $this->setExpectedException('Tinebase_Exception_Record_NotAllowed');
        try {
            $recordArray[] = new Tinebase_Record_DummyRecord(array(), true);
            $recordArray[] = new Tinebase_Model_Container(array(), true);
            $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord', $recordArray);
        } catch (InvalidArgumentException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    /**
     * test addition of a record
     */
    public function testAddRecords ()
    {
        $this->assertEquals(4, count($this->object));
    }
    
    public function testAddIdLessRecords()
    {
        $this->assertEquals(array(0, 1), $this->object->getIdLessIndexes());
    }
    
    public function testAddIdFullRecords()
    {
        $this->assertEquals(array(1, 2), $this->object->getArrayOfIds());
    }
    
    /**
     * test if exception is thrown when adding record of wrong type
     *
     */
    public function testAddWrongRecordException ()
    {
        $this->setExpectedException('Tinebase_Exception_Record_NotAllowed');
        try {
            $record = new Tinebase_Model_Container(array(), true);
            $this->object->addRecord($record);
        } catch (InvalidArgumentException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    
    /**
     * checkes if isValid of of member records is called
     */
    public function testIsValid ()
    {
        $record = new Tinebase_Record_DummyRecord(array('id' => 'shouldBeInt'), true);
        $this->object->addRecord($record);
        $record->bypassFilters = false;
        $this->assertFalse($this->object->isValid());
    }
    /**
     * test implementation of IteratorAggregate
     */
    public function testIteratorAggregate ()
    {
        $gotIterator = false;
        foreach ($this->object as $record) {
            $gotIterator = true;
            $this->assertTrue($record instanceof Tinebase_Record_DummyRecord);
        }
        $this->assertTrue($gotIterator);
    }
    /**
     * test toArray() implementation
     */
    public function testToArray ()
    {
        $resultArray = $this->object->toArray();
        for ($i=0; $i < count($this->object); $i++) {
            $this->assertEquals($this->object[$i]->toArray(), $resultArray[$i]);
        }
    }
    /**
     * test__set().
     */
    public function test__set ()
    {
        $id = rand(1,100);
        $this->object->id = $id;
        foreach ($this->object as $record) {
            $this->assertEquals($id, $record->id);
        }
    }
    
    /**
     * test__call().
     */
    public function test__call ()
    {
        $now = Zend_Date::now();
        $now->setTimezone('Europe/Berlin');
        
        $this->object->date_single = clone ($now);
        $this->object->setTimezone('America/Los_Angeles');
        foreach ($this->object as $record) {
        	$this->assertNotEquals($record->date_single->get(Tinebase_Record_Abstract::ISO8601LONG), $now->get(Tinebase_Record_Abstract::ISO8601LONG));
        }
        
    }
    /**
     * testCount().
     */
    public function testCount ()
    {
        $before = $this->object->count();
        $record = new Tinebase_Record_DummyRecord(array(), true);
        $this->object->addRecord($record);
        $this->assertEquals($this->object->count(), ($before + 1));
    }
    /**
     * testOffsetExists().
     */
    public function testOffsetExists ()
    {
        $count = count($this->object);
        $this->assertFalse(isset($this->object[$count]));
        $record = new Tinebase_Record_DummyRecord(array(), true);
        $this->object->addRecord($record);
        $this->assertTrue(isset($this->object[$count]));
    }
    /**
     * testOffsetGet().
     */
    public function testOffsetGet ()
    {
        $record = new Tinebase_Record_DummyRecord(array(), true);
        $index = $this->object->addRecord($record);
        $this->assertEquals($record, $this->object[$index]);
    }
    /**
     * testExistingOffsetSet().
     */
    public function testExistingOffsetSet()
    {
        $count = count($this->object);
        $this->object[0] = new Tinebase_Record_DummyRecord(array('id' => 3, 'string' => 'idFull3'), true); 
        $this->object[2] = new Tinebase_Record_DummyRecord(array('string' => 'idLess3'), true); 
        
        $this->assertEquals($count, count($this->object), 'To many records in recordSet');
        $this->assertEquals(array(1, 2), $this->object->getIdLessIndexes(), 'wrong idLess indexes');
        $this->assertEquals(array(2, 3), $this->object->getArrayOfIds(), 'wrong idFull indexes');
    }
    
    public function testNewOffsetSet()
    {
        $count = count($this->object);
        $this->object[] = new Tinebase_Record_DummyRecord(array('string' => 'idLess3'), true); 
        $this->assertEquals($count+1, count($this->object), 'To many records in recordSet');
    }
    
    public function testNonExistantOffsetSet()
    {
        $this->setExpectedException('Tinebase_Exception_Record_NotAllowed');
        $this->object[99] = new Tinebase_Record_DummyRecord(array('string' => 'error'), true);
    }
    
    public function testNonRecordOffsetSet()
    {
        $this->setExpectedException('Tinebase_Exception_Record_NotAllowed');
        $this->object[] = array();
    }
    
    public function testGetIndexById()
    {
        $idx = $this->object->getIndexById(1);
        $this->assertEquals(2, $idx);
    }
    
    /**
     * testOffsetUnset().
     */
    public function testOffsetUnset()
    {
        unset($this->object[1]);
        unset($this->object[3]);
        $this->assertEquals(2, count($this->object));
        $this->assertEquals(array(0), $this->object->getIdLessIndexes(), 'wrong idLess indexes');
        $this->assertEquals(array(1), $this->object->getArrayOfIds(), 'wrong idFull indexes');
    }
    
    /**
     * test filter function 
     */
    public function testFilter()
    {
        // get subset with filter
        $subset = $this->object->filter('string', 'idLess1');     
        
        $subsetArray = $subset->toArray();
        $record = array_pop($subsetArray);
        //print_r($record);
        
        $this->assertEquals(1, count($subset));
        $this->assertEquals(4, count($this->object));
        $this->assertEquals('idLess1', $record['string']);
    }    
    
    /**
     * test validation errors 
     */
    public function testGetValidationErrors()
    {
        $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('string' => 'idLess1'), true));
        //$record = $this->object[0];
        if(!$this->object->isValid()) {
            $errors = $this->object->getValidationErrors();
        }
    }
    
    /**
     * test get migration 
     */
    public function testGetMigration()
    {
        $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '100', 'string' => 'Test1'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '200', 'string' => 'Test2'), true));
        $result = $this->object->getMigration(array('100', '200'));
    }
    
    /**
     * test translate 
     */
    public function testTranslate()
    {
        $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '100', 'string' => 'Test1'), true));
        $result = $this->object->translate();
    }
    
    /**
     * test set by indices 
     */
    public function testSetByIndices()
    {
        $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '100', 'string' => 'Test1'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '200', 'string' => 'Test2'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '300', 'string' => 'Test3'), true));
        $result = $this->object->setByIndices('string', array('a', 'b', 'c'));
    }
    
    /**
     * test sort 
     */
    public function testSort()
    {
        $this->object = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '100', 'string' => 'Test3'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '200', 'string' => 'Test1'), true));
        $this->object->addRecord(new Tinebase_Record_DummyRecord(array('id' => '300', 'string' => 'Test2'), true));
        $result = $this->object->sort('string', 'ASC');
    }
    
    public function testSimpleFilter()
    {
        $recordSet = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $recordSet->addRecord(new Tinebase_Record_DummyRecord(array('id' => '100', 'string' => 'bommel'), true));
        $recordSet->addRecord(new Tinebase_Record_DummyRecord(array('id' => '200', 'string' => 'super'), true));
        $recordSet->addRecord(new Tinebase_Record_DummyRecord(array('id' => '300', 'string' => 'bommel'), true));
        
        $filterResultWOIndices = $recordSet->filter('string', 'bommel');
        $this->assertEquals(2, count($filterResultWOIndices));
        $this->assertEquals(array(100, 300), $filterResultWOIndices->getArrayOfIds());
        
        $recordSet->addIndices(array('string'));
        $filterResultWIndices = $recordSet->filter('string', 'bommel');
        $this->assertEquals(count($filterResultWOIndices), count($filterResultWIndices));
        $this->assertEquals(array(100, 300), $filterResultWIndices->getArrayOfIds());
    }
    
    public function testRegexpFilter()
    {
        $recordSet = new Tinebase_Record_RecordSet('Tinebase_Record_DummyRecord');
        $recordSet->addRecord(new Tinebase_Record_DummyRecord(array('id' => '100', 'string' => 'bommel-1'), true));
        $recordSet->addRecord(new Tinebase_Record_DummyRecord(array('id' => '200', 'string' => 'super-1'), true));
        $recordSet->addRecord(new Tinebase_Record_DummyRecord(array('id' => '300', 'string' => 'bommel-2'), true));
        
        $filterResultWOIndices = $recordSet->filter('string', '/^bommel.*/', TRUE);
        $this->assertEquals(2, count($filterResultWOIndices));
        $this->assertEquals(array(100, 300), $filterResultWOIndices->getArrayOfIds());
        
        $recordSet->addIndices(array('string'));
        $filterResultWIndices = $recordSet->filter('string', '/^bommel.*/', TRUE);
        $this->assertEquals(count($filterResultWOIndices), count($filterResultWIndices));
        $this->assertEquals(array(100, 300), $filterResultWIndices->getArrayOfIds());
    }
}
// Call Tinebase_Record_RecordSetTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Tinebase_Record_RecordSetTest::main') {
    Tinebase_Record_RecordSetTest::main();
}
