<?php
require_once('typo3conf/ext/wildsidemvc/jsonencoder.php');
require_once('tx_wildsidemvc_ibasemodel.php');
require_once('tx_wildsidemvc_imodelhelper.php');
abstract class tx_wildsidemvc_abstractbasemodel implements tx_wildsidemvc_ibasemodel, tx_wildsidemvc_imodelhelper {
    /**
     * The name of the table, has to be set in the constructor of each class.
     */
    protected $tableName = '';
    protected $stringFields;
    protected $jsonEncoder;
    protected $returnMethod = '';
    /**
     *
     *
     * @var tx_wildsidemvc_log
     */
    protected $logger;
    /**
     *
     *
     * @var tx_wildsidemvc_modelfactory
     */
    protected $modelFactory;

    /**
     * Set the return method of this class. 0, 1 (json ready), 2 (json string)
     * @return
     * @param $method int 		A value reprenting which return method the class should use. 0, 1 (json ready), 2 (json string)
     */
    public function setReturnMethod($method) {
            $this->returnMethod = $method;
    }



    /**
     * A method to parse the selected table for stringtype fields.
     * @return
     */
    protected function getStringFieldsFromTable() {
            $sql = "SHOW fields FROM ".$this->tableName." WHERE type LIKE 'varchar%' OR type LIKE 'char%' OR type LIKE '%text%';";
            $this->log($sql);
            $list = $this->getListAsArray($sql);
            $tempArr = array();
            $i = 0;

            foreach($list as $row) {
                    $this->log('String field: '.$this->tableName . '.' . $row['Field']);
                    $tempArr[$i] = 	$row['Field'];
                    $i++;
            }
            $this->stringFields = $tempArr;
    }

    /**
     * A method for reading the stringFields form outside this class. (Primarily for debugging purposes at the moment)
     * @return
     */
    public function getStringFields() {
            return $this->stringFields;
    }


    public function __construct($tn, &$modelFactory) {
        $this->tableName = $tn;
        $this->modelFactory = $modelFactory;
    }

    public function setLogger(tx_wildsidemvc_ilogger &$logger) {
        $this->logger = $logger;

        $this->logger->logmsg('Logging added to model ' . get_class($this));
    }

    public function log($msg) {
        if(!is_null($this->logger)) {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
            $this->logger->logmsg(get_class($this) . '->' . $methodName . ': ' . $msg);
        }
    }

    public function getConfigurationValue($field, $table='') {
		if($table=='') {
			$table = $this->tableName;
		}
		$conf = $this->modelFactory->getModelConfByTable($table);
		$this->log('Table: '.$table);
		$this->log('Field: '.$field);
		$this->log('Value: '.$conf[$field]);
		return $conf[$field];
	}


	/**
	 * Method for getting the EconomicWebserviceClient
	 *
	 * @return EconomicWebServiceClient
	 */
	protected function getEconomicService() {
		$agreementNumber = $this->getConfigurationValue('economic_agreementNumber');
		$userName = $this->getConfigurationValue('economic_userName');
		$password = $this->getConfigurationValue('economic_passWord');
		$client = new EconomicWebServiceClient ( 'https://www.e-conomic.com/secure/api1/EconomicWebservice.asmx?WSDL' );
		$client->Connect ( array ('agreementNumber' => $agreementNumber, 'userName' => $userName, 'password' => $password ) );
		return $client;
	}




    /*
     * Implementing interface tx_wildsidemvc_imodelhelper
     *
     *
     *
     *
     *
     */

    /**
     * Gets an order by clause by setting field and direction.
     * @return
     * @param $orderField string			The field which should be used for ordering.
     * @param $direction string[optional]	Either ASC for ascending, og DESC for DESCENDING.
     */
    public function getOrderBy($orderField, $direction="ASC") {
            return 'ORDER BY ' . $orderField . ' ' . $direction;
    }

    /**
     * Gets a ASCending sorting (requires the sorting field in the table)
     * @return
     */
    public function sortingAsc() {
            return $this->getOrderBy('sorting');
    }

    /**
     * Gets a DESCending sorting (requires the sorting field in the table)
     * @return
     */
    public function sortingDesc() {
            return $this->getOrderBy('sorting', 'desc');

    }

    /**
     * Get a limit clause
     * @return
     * @param $number 	int				the number of records to return.
     * @param $start 	int[optional] 	the row from which to start.
     */
    public function getLimit($number, $start=0) {
            return 'LIMIT '.$start.', '.$number;
    }

    /**
     * For pagianated views it is important to get a given page of objects.
     * @return
     * @param $page int[optional]			The page number to show.
     * @param $numberPrPage int[optional]	The number of objects pr page.
     */
    public function getPage($page=0, $numberPrPage=10) {
            $start = $numberPrPage*$page;
            return $this->getLimit($numberPrPage, $start);
    }

    /**
     * Generic where field
     * @return
     * @param $fieldName string[optional] 		Fieldname
     * @param $fieldValue Object[optional]		The value of the field
     * @param $equalSign string[optional]		A character or string for the logical operation =, <, > or LIKE
     * @param $prependWith string[optional]		WHERE, AND, OR or a empty string
     * @param $isStringValue boolean[optional]	Sets wether the value is a string or a literal (fieldname, true/false or numeric value)
     */
    protected function getWhereField($fieldName="", $fieldValue="", $equalSign="=", $prependWith="AND", $isStringValue = false) {
                    if($deadline)
                            $deadline = mktime();
                    if($isStringValue)
                            $fieldValue = '"' . $fieldValue . '"';
                    return $prependWith . ' ' . $fieldName . $equalSign . $fieldValue;
    }

    /**
     * Method for getting a where clause for a single numeric field
     * @return
     * @param $value int[optional] 			Value
     * @param $prependWith string[optional] 	String to prepend the where string with (defaults to AND, use empty string, "AND", "OR" or "WHERE")
     */
    public function getWhereNumericField($fieldName, $value=0, $equalSign="=", $prependWith="AND") {
                    return $this->getWhereField($fieldName, $value, $equalSign, $prependWith);
    }


    /**
     * Method for getting a where clause for a range between two values on a single numeric field.
     * @return
     * @param $deadlineStart int[optional]			Deadline as a linux timestamp
     * @param $deadlineEnd int[optional]			Deadline as a linux timestamp
     * @param $prependWith string[optional]			String to prepend the where string with (defaults to AND, use empty string, "AND", "OR" or "WHERE")
     * @param $parantesis boolean[optional]			Wether the inner statement should be wrapped in parantesis
     */
    public function getWhereNumericRange($fieldName, $valueStart=0, $valueEnd=0, $prependWith="", $parantesis=false) {
            if($valueStart==$valueEnd)
                    return $this->getWhereNumericField($valueStart, '=', $prependWith);

            $where = $this->getWhereNumericField($fieldName, $valueStart, '>', '');
            $where .= ' ';
            $where .= $this->getWhereNumericField($fieldName, $valueEnd, '<', 'AND');
            if($parantesis) {
                    $where = '(' . $where . ')';
            }
            return $prependWith . ' ' . $where;
    }



    /*
     * Implementing tx_wildsidemvc_ibasemodel
     *
     *
     *
     *
     *
     */


    /**
     * Gets a single datarow from the id of the row
     * @return the data row as array
     * @param $uid int	The id of the row
     */
    public function getFromId($uid) {
            return $this->getSingle('SELECT * FROM '.$this->tableName.' WHERE uid = ' . $uid);
    }

    /**
     *
     * @param <string> $uidList     Commaseparated list of uid's
     * @return <type>
     */
    public function getFromIdList($uidList) {
        return $this->getAll("WHERE uid in (".$uidList.");");
    }

    /**
     * Gets all data from a table - apply a where-clause or a limit-clause to minimize the number
     * of returned rows.
     * @param $where string[optional] 	A where clause as a string - needs to be prepended with the WHERE keyword
     * @param $order string[optional] 	A order by clause - has to be prepended with the ORDER BY keywords
     * @param $limit string[optional] 	A limit clause - has to be prepended with LIMIT keyword
     * @return the datarows as an array of arrays (rows)
     */
    public function getAll($where="", $order="", $limit="") {
            $sql = $this->getSelectSql('*', $this->tableName, $where, $order, $limit);
            return $this->getList($sql);

    }

    /**
     * Gets an array of datarows from the table - where the field in the row,
     * equals the value.
     * @return
     * @param $field string 			Fieldname - has to be valid in the databasetable
     * @param $value int				Field value - has to be a valid numeric value (integer)
     * @param $order string[optional] 	A order by clause - has to be prepended with the ORDER BY keywords
     * @param $limit string[optional] 	A limit clause - has to be prepended with LIMIT keyword
     */
    public function getFromRelation($field, $value, $orderBy="", $limit="", $addWhere = "") {
            $where = $this->getWhereField($field, $value, '=', 'WHERE', false) . ' ' . $addWhere;
            return $this->getAll($where, $orderBy, $limit);
    }

    /**
     * Returns a valid sql statement based on fields, tables, where-clause, order-clause and limit.
     * @return
     * @param $getFields string				A commaseparated list of fields
     * @param $getTables string[optional]	A commaseparated list of tables
     * @param $where string[optional] 		A where clause as a string - needs to be prepended with the WHERE keyword
     * @param $order string[optional] 		A order by clause - has to be prepended with the ORDER BY keywords
     * @param $limit string[optional] 		A limit clause - has to be prepended with LIMIT keyword
     */
    protected function getSelectSql($getFields, $getTables="", $where="", $order="", $limit="") {
            if($getTables=="")
                    $getTables = $this->tableName;
            $sql = 'SELECT ' . $getFields . ' FROM ' . $getTables . ' ' . $where . ' ' . $order . ' ' . $limit;

        return $sql;
    }

    /**
     * Gets an array of datarows from the table corresponding to the sql statement from the fields.
     * @return
     * @param $getFields string				A commaseparated list of fields
     * @param $getTables string[optional]	A commaseparated list of tables
     * @param $where string[optional] 		A where clause as a string - needs to be prepended with the WHERE keyword
     * @param $order string[optional] 		A order by clause - has to be prepended with the ORDER BY keywords
     * @param $limit string[optional] 		A limit clause - has to be prepended with LIMIT keyword
     */
    public function getSelectList($getFields, $getTables="", $where="", $order="", $limit="") {
            $sql = $this->getSelectSql($getFields, $getTables, $where, $order, $limit);
            return $this->getList($sql);
    }


    public function getSingle($sql) {
	$this->log($sql);
        switch($this->returnMethod) {
	    case 0:
		return $this->getSingleAsArray($sql);
	    break;
	    case 1:
		return $this->getSingleAsEntityArray($sql);
	    break;
	    case 2:
		return $this->getSingleAsJson($sql);
	    break;
	}
    }

    /**
     * Method returning the result (1-n rows) of the call to the database with the resultmethod the class has been set to.
     * @return
     * @param $sql string	Sql query
     */
    public function getList($sql) {
        $this->log($sql);
        //t3lib_div::debug($this->returnMethod, 'getList returnmethod');
        //t3lib_div::debug($sql, 'getList sql');
          switch($this->returnMethod) {
                    case 0:
                            return $this->getListAsArray($sql);
                    break;
                    case 1:
                            return $this->getListAsEntityArray($sql);
                    break;
                    case 2:
                            return $this->getListAsJson($sql);
                    break;
            }
    }

    /**
     * Function to return a single datarow from a sql statement.
     * @return
     * @param $sql string		Sql statement
     */
    protected function getSingleAsArray($sql) {
            $res = mysql(TYPO3_db, $sql);
            $row=mysql_fetch_array($res, MYSQL_ASSOC);
            return $row;
    }

    /**
     * Function to return an array of datarows from a sql statement.
     * @return
     * @param $sql string		Sql statement
     */
    protected function getListAsArray($sql) {
            $res = mysql(TYPO3_db, $sql);
            //echo $sql;
            $rows = array();
            $i=0;
            $row=mysql_fetch_array($res, MYSQL_ASSOC);
            while($row) {
                    $rows[$i]=$row;
                    $i++;
                    $row=mysql_fetch_array($res, MYSQL_ASSOC);
            }
            return $rows;

    }

    /**
     * Function to return a single datarow as Json encoded string from a sql statement.
     * @return
     * @param $sql string		Sql statement
     */
    protected function getSingleAsJson($sql) {
            $res = mysql(TYPO3_db, $sql);
            $row = mysql_fetch_array($res, MYSQL_ASSOC);
            $row = $this->jsonEncoder->parseDataRow($row, $this->stringFields);
            $rval =  $this->jsonEncoder->encodeJson($row);
            return $rval;
    }

    /**
     * Function to return an array of datarows as Json encoded string from a sql statement.
     * @return
     * @param $sql string		Sql statement
     */
    protected function getListAsJson($sql) {

            $res = mysql(TYPO3_db, $sql);
	    $rows = array();
            $i=0;
            $row=mysql_fetch_array($res,MYSQL_ASSOC);
            while($row) {
                    $row = $this->jsonEncoder->parseDataRow($row, $this->stringFields);
                    $rows[$i]=$row;
                    $i++;
                    $row=mysql_fetch_array($res, MYSQL_ASSOC);
            }
            $rrows= array();
            $rrows['identifier'] = "uid";
            $rrows['items'] = $rows;
            $rval = json_encode($rrows);
            return $rval;
    }

    /**
     * Function to return a single datarow where all string elements has been prepared for json output.
     * @return
     * @param $sql string		Sql statement
     */
    protected function getSingleAsEntityArray($sql) {
            $res = mysql(TYPO3_db, $sql);
            $row = mysql_fetch_array($res, MYSQL_ASSOC);
            $row = $this->jsonEncoder->parseDataRow($row, $this->stringFields);
            return $row;
    }

    /**
     * Function to return an array of datarows where all string elements has been prepared for json output.
     * @return
     * @param $sql string		Sql statement
     */
    protected function getListAsEntityArray($sql) {
            $res = mysql(TYPO3_db, $sql);
            $rows = array();
            $i=0;
            $row=mysql_fetch_array($res,MYSQL_ASSOC);
            while($row) {
                    $row = $this->jsonEncoder->parseDataRow($row, $this->stringFields);
                    $rows[$i]=$row;
                    $i++;
                    $row=mysql_fetch_array($res,MYSQL_ASSOC);
            }
            return $rows;
    }

    /**
     * Checks wether the given field is a string field or not.
     * @param <string> $fieldName     The field name
     * @return <boolean>              True or false
     */
    protected function isStringField($fieldName) {
        $this->log('testing wether ' . $fieldName . ' is a stringField');
        return in_array($fieldName, $this->stringFields);
    }

    protected function quoteString($str) {
        return '"'.$str.'"';
    }

    protected function runSql($sql) {
        $res = mysql(TYPO3_db, $sql);
    }

}
?>