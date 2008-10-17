<?php
require_once('typo3conf/ext/wildsidemvc/models/tx_wildsidemvc_basemodel.php');
require_once('typo3conf/ext/wildsidemvc/controllers/tx_wildsidemvc_baseController.php');
require_once('typo3conf/ext/wildsidemvc/controllers/tx_wildsidemvc_errorController.php');
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_log.php');
require_once('typo3conf/ext/wildsidemvc/helpers/tx_wildsidemvc_sanitizeHelper.php');
require_once('typo3conf/ext/wildsidemvc/helpers/tx_wildsidemvc_formathelper.php');
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_configurationmanager.php');
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_modelfactory.php');
/**
 * This class handles web-requests. It consists of only one public method called handle, which is the actual requestHandler. 
 */
class tx_wildsidemvc_requesthandler {
  var $extkey = 'wildsidemvc';
  /**
   * The actual request handler. It is responsible for delegating requests to controllers. The main delegating is done by a switch statement
   * which filters the cmd of a request (typically a get og post var) and calls the appropiate controller with the request object. 
   * @param <type> $request
   * @return <type>
   */
  public function handle(&$request) {
    $confObject = new tx_wildsidemvc_configurationmanager($this->extkey);
    $conf = $confObject->getConfigurationArray();
    
    if($this->extkey) {
        
    }
    if(!isset($request['logger'])) {
        $request['logger'] = new tx_wildsidemvc_log($this->extkey);
    }
    
    $request['logger']->log($request);
    $modelFactory = new tx_wildsidemvc_modelfactory($conf, $request['logger']);
    if(!isset($request['sanitizer'])) {
      $request['sanitizer'] = new tx_wildsidemvc_sanitizeHelper();
    }
    
    if(!isset($request['formatter'])) {
      $request['formatter'] = new tx_wildsidemvc_formathelper();
    }
    
      
    
    
    $mainController = null;
    $controllerPath = t3lib_extMgm::extPath($this->extkey).'controllers/tx_'.$this->extkey.'_'.$request['cmd'].'Controller.php';
    $mainControllerMethod = '$mainController = new tx_'.$this->extkey.'_'.$request['cmd'].'Controller($this->extkey, $modelFactory);';
    if(file_exists($controllerPath)) {
        require_once($controllerPath);
        try {

          eval($mainControllerMethod);
        }
        catch( Exception $e ) {
          $mainControllerMethod = '$mainController = new tx_wildsidemvc_errorController($this->extkey, $modelFactory);';
          eval($mainControllerMethod);
        }

        
    }
    else {
       $mainControllerMethod = '$mainController = new tx_wildsidemvc_errorController($this->extkey);';
       eval($mainControllerMethod);
    }
    $mainController->setLogger($request['logger']);
    $mainController->setSanitizer($request['sanitizer']);
    $mainController->setFormatter($request['formatter']);
    return $mainController->handle($request);
  } 
  
  /**
   * Sets the parent extension (the extension key of the extension using the framework.) 
   * @param <type> $extensionName
   */
  public function setParentExtension($extensionName) {
      $this->extkey = $extensionName;
  }
}
?>