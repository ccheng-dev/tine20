<?php
/**
 * Tine 2.0
 *
 * @package     Sales
 * @subpackage  Backend
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schuele <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2007-2009 Metaways Infosystems GmbH (http://www.metaways.de)
 */


/**
 * backend for contracts
 *
 * @package     Sales
 * @subpackage  Backend
 */
class Sales_Backend_Contract extends Tinebase_Backend_Sql_Abstract
{
    /**
     * Table name without prefix
     *
     * @var string
     */
    protected $_tableName = 'sales_contracts';
    
    /**
     * Model name
     *
     * @var string
     */
    protected $_modelName = 'Sales_Model_Contract';

    /**
     * default column(s) for count
     *
     * @var string
     */
    protected $_defaultCountCol = 'id';
    
    /**
     * if modlog is active, we add 'is_deleted = 0' to select object in _getSelect()
     *
     * @var boolean
     */
    protected $_modlogActive = TRUE;

    /**
     * returns all ids of contracts by interval. last_autobill doesn't get respected here.
     *
     * @param Tinebase_DateTime $date
     * @return array
     */
    public function getBillableContractIds(Tinebase_DateTime $date)
    {
        $date = clone $date;
        $date->setTimezone('UTC');
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->setTime(0,0,0);
        $date->submonth(6);

        $be = new Sales_Backend_Contract();
        $db = $be->getAdapter();

        $sql = 'SELECT ' . $db->quoteIdentifier('id') . ' FROM ' . $db->quoteIdentifier(SQL_TABLE_PREFIX . 'sales_contracts') .
        ' WHERE (' . $db->quoteInto($db->quoteIdentifier('end_date') . ' >= ?', $date) . ' OR ' . $db->quoteIdentifier('end_date') . ' IS NULL ) ';
        
        $date->addMonth(7);
        $date->subSecond(1);
        $sql .= ' AND ' . $db->quoteInto($db->quoteIdentifier('start_date') . ' <= ?', $date)
              . ' AND ' . $db->quoteIdentifier('start_date') . ' IS NOT NULL'
              . ' AND ' . $db->quoteIdentifier('start_date') . ' <> "0000-00-00 00:00:00"';
    
        return array_keys($db->fetchAssoc($sql));
    }
}
