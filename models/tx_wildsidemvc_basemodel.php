<?php
/**
 * Base model class. This class contains some helper methods.
 * This class can be used either on its own, called via a constructor, which sets the
 * name of the table - but this only gives you a limited and generic set of usages.
 *
 * A better usage is to inherit from this class and use its methods as helpers to build more
 * complex usages and queries for the database.
 *
 * Allways return data with this class' getSingle or getList methods - or deriviants thereoff.
 *
 * @TODO: Add logging
 * @TODO: Add exception handling
 */
require_once('tx_wildsidemvc_abstractbasemodel.php');
require_once('tx_wildsidemvc_icreatemodel.php');
require_once('tx_wildsidemvc_iupdatemodel.php');
class tx_wildsidemvc_basemodel extends tx_wildsidemvc_abstractbasemodel implements tx_wildsidemvc_iupdatemodel, tx_wildsidemvc_icreatemodel {
    
    public function __construct($tn, &$modelFactory) {
        parent::__construct($tn, $modelFactory);
    }
    
    
    
    /**
     * Inserts a row in the current table. It automatically quotes the string fields. 
     * @param <type> $fields
     */
    public function insertRow($fields) {
        $this->getStringFieldsFromTable();
        $sqlFields = array();
        $sqlData = array();
        foreach($fields as $key=>$value) {
            if($this->isStringField($key)) {
                $value = $this->quoteString($value);
                
            }
            $sqlFields[] = $key;
            $sqlData[] = $value;
        }
        $sql = 'INSERT INTO ' . $this->tableName . ' (' . implode(',', $sqlFields) . ') VALUES ('. implode(',', $sqlData) . ');';
        $this->log($sql);
        $this->runSql($sql);
    }
    
    /**
     * Updates any given number of rows in the current table. It automatically quotes the string fields. 
     * @param <type> $fields
     * @param <type> $where
     */
    public function updateRow($fields, $where) {
        $this->getStringFieldsFromTable();
        $sqlArray = array();
        foreach($fields as $key=>$value) {
            if($this->isStringField($key)) {
                $this->log('Quoting field ' . $key);
                $value = $this->quoteString($value);
            } else {
                $this->log('Not quoting field ' . $key);
            }
            $sqlArray[] = $key.'='.$value;
        }
        $sql = 'UPDATE ' . $this->tableName . ' SET ' . implode(',',$sqlArray) . ' ' . $where;
        $this->log($sql);
        $this->runSql($sql);
    }
 }
?>