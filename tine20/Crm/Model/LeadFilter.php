<?php
/**
 * Tine 2.0
 * 
 * @package     Crm
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schuele <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2007-2008 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id$
 *
 */

/**
 * Leads Filter Class
 * @package Crm
 */
class Crm_Model_LeadFilter extends Tinebase_Model_Filter_FilterGroup
{
    /**
     * @var string class name of this filter group
     *      this is needed to overcome the static late binding
     *      limitation in php < 5.3
     */
    protected $_className = 'Crm_Model_LeadFilter';
    
    /**
     * @var string application of this filter group
     */
    protected $_applicationName = 'Crm';
    
    /**
     * @var array filter model fieldName => definition
     */
    protected $_filterModel = array(
        'query'          => array('filter' => 'Tinebase_Model_Filter_Query', 'options' => array('fields' => array('lead_name', 'description'))),
        'description'    => array('filter' => 'Tinebase_Model_Filter_Text'),
        'lead_name'      => array('filter' => 'Tinebase_Model_Filter_Text'),
        'tag'            => array('filter' => 'Tinebase_Model_Filter_Tag'),
        'probability'    => array('filter' => 'Tinebase_Model_Filter_Int'),
        'turnover'       => array('filter' => 'Tinebase_Model_Filter_Int'),
        'leadstate_id'   => array('filter' => 'Tinebase_Model_Filter_Int'),
        'container_id'   => array('filter' => 'Tinebase_Model_Filter_Container', 'options' => array('applicationName' => 'Crm')),
        'showClosed'     => array('custom' => true),
    );

    /**
     * appends current filters to a given select object
     * 
     * @param  Zend_Db_Select
     * @return void
     */
    public function appendFilterSql($_select)
    {
        $db = Tinebase_Core::getDb();
        
        $showClosed = false;
        foreach ($this->_customData as $customData) {
            if ($customData['field'] == 'showClosed' && $customData['value'] == true) {
                $showClosed = true;
            }
        }
        
        if($showClosed){
            // nothing to filter
        } else {
            $_select->where($db->quoteIdentifier('end') . ' IS NULL');
        }
        
        
        //Tinebase_Core::getLogger()->debug(__METHOD__ . '::' . __LINE__ . ' ' . $_select->__toString());
        
        parent::appendFilterSql($_select);
    }
}
