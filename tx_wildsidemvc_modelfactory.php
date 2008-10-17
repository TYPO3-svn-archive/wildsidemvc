<?php
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_imodelfactory.php');
class tx_wildsidemvc_modelfactory implements tx_wildsidemvc_imodelfactory{
    public $configuration;
    private $objectByNameArray;
    protected $logger;

    public function __construct(&$configuration, tx_wildsidemvc_log &$logger) {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->logger->logmsg('Logging added to controller ' . get_class($this));
        $this->objectByNameArray = array();
    }



    public function getModelByName($modelName) {
        $this->log('Get model object for name:'.$modelName);
    	$models = $this->configuration;
        $modelArray = $this->findModel($models, $modelName, '');
        $this->log($modelArray);
        $this->log('Modelname: '.$modelArray['name']);
        $this->log('Model classname: '.$modelArray['classname']);
        return $this->getModelInstance($modelArray);
    }

    public function getModelByTable($tableName) {
        $this->log('Get model object for table:'.$tableName);
    	$models = $this->configuration;
        $modelArray = $this->findModel($models, '', $tableName);
        $this->log('modelArray is_null? : '.is_null($modelArray));
        $this->log('modelArray is_empty? : '.empty($modelArray));
        if(is_null($modelArray)) {
            $this->log('Getting baseclass');
        	return $this->getBasemodelInstance($tableName);
        }
        $this->log('Getting existing class');
        return $this->getModelInstance($modelArray);
    }

    public function getModelConfByTable($tableName) {
    	$this->log('Get model object for table:'.$tableName);
    	$models = $this->configuration;
        $modelArray = $this->findModel($models, '', $tableName);
        $this->log('modelArray is_null? : '.is_null($modelArray));
        $this->log('modelArray is_empty? : '.empty($modelArray));
        return $modelArray;
    }

    public function getModelConfByName($modelName) {
		$this->log('Get model object for name:'.$modelName);
    	$models = $this->configuration;
        $modelArray = $this->findModel($models, $modelName, '');
        return $modelArray;
    }

    /****************/

    private function log($msg) {
        if(!is_null($this->logger)) {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
            $this->logger->logmsg(get_class($this) . '->' . $methodName . ': ' . $msg);
        }
    }

    private function findModel($models, $modelName="", $tableName="") {
        $model = null;
        if($modelName!="") {
            $model = $this->findModelByNodeName($models, 'name', $modelName);
        } else if($tableName!="") {
            $model = $this->findModelByNodeName($models, 'table', $tableName);
        } else {
            $model = null;
        }
        return $model;
    }

    private function findModelByNodeName($models, $nodeName, $modelName) {
        foreach($models as $model) {
            $this->log('Checking if ('.$nodeName.') *' . $model[$nodeName] . '* equals *' . $modelName.'*');
        	if($model[$nodeName]==$modelName) {
                $this->log('Match');
        		return $model;
            }
        }
    }

    private function getModelInstance($modelArray) {
        if(is_null($this->objectByNameArray[$modelArray['name']])) {
			$this->log('Object ('.$modelArray['name'].')does not exist - instantiating');
    	   	$this->objectByNameArray[$modelArray['name']] = $this->getModelClassInstance($modelArray);
    	   	$this->log('Object instantiation complete');
        }
        $this->log('Returning objectByNameArray: ' . $modelArray['name']);
        return $this->objectByNameArray[$modelArray['name']];
    }

    private function getBasemodelInstance($table) {
        $method = '$class = new tx_wildsidemvc_basemodel($table, $this);';
        $this->log('Instantiating with method:' . $method);
        eval($method);
        return $class;
    }

    private function getModelClassInstance($modelArray) {
        $method = '$class = new '.$modelArray['classname'].'($this);';
        $this->log('Instantiating with method:' . $method);
        $this->log('Instantiating class: ' . $modelArray['classname']);
        eval($method);
        return $class;
    }
}
?>
