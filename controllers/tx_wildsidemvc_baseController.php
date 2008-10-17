<?php
require_once('typo3conf/ext/wildsidemvc/models/tx_wildsidemvc_basemodel.php');
require_once('typo3conf/ext/wildsidemvc/models/tx_wildsidemvc_dataobject_arrayhandler.php');

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once (PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once (PATH_tslib.'class.tslib_content.php');
require_once (PATH_tslib.'class.tslib_gifbuilder.php'); 

/**
 * Base controller - all controllers needs this required, and must inherit from this, for the framework to work
 * correctly. 
 * 
 * EXAMPLE: 
 * <?php
 * require_once('typo3conf/ext/wildsidemvc/controllers/tx_wildsidemvc_baseController.php');
 * 
 ' class tx_yourextension_namedController extends tx_wildsidemvc_baseController {
 *    public function __construct($extkey) {
 *      parent::__construct($extkey);
 *    }
 * }
 * 
 * The simplest methods of a controller are methods which does nothing but return the view. The view is automatically 
 * located. It has to be in the folder EXT:yourextension/views/controllername/methodname.html
 * 
 * EXAMPLE: 
 * protected function main($request) {
 *    $view = $this->getView();
 *    return $view;
 * }
 * 
 * You can off course use Typo3s template methods in your controller. The returned view will then be your 
 * basis template for this. 
 * 
 * EXAMPLE: 
 * protected function main($request) {
 *    $view = $this->getView();
 *    $view = $this->cObj->getSubpart($view,'###TEMPLATE###');
 *    $markerArray['###MARKER1###'] = 'content for marker 1';
 *    $markerArray['###MARKER2###'] = 'content for marker 2';
 *    $content = $this->cObj->substituteMarkerArrayCached($view,$markerArray);
 *    return $content;
 * }
 *   
 * ?>
 */
class tx_wildsidemvc_baseController {
    var $cObj;
    var $tempGlobals;
    var $extkey;
    private $logger;
    public $sanitizer;
    public $formatter;
    protected $modelFactory;
    /**
     * Constructor for the base class, has to be overridden, and called with parent::__construct($extkey, &$modelFactory);
     * @param <type> $extkey
     */
    public function __construct($extkey, &$modelFactory) {
        //Typo3's cobject is included to make it possible to use typo3's file and template (subpart) methods
        $this->controller = get_class($this);
        $this->cObj = t3lib_div::makeInstance('tslib_cObj');	// Local cObj.
        $this->cObj->start(array());
        $this->extkey = $extkey;
        $this->modelFactory = $modelFactory;
    }
    
    public function setLogger(tx_wildsidemvc_log &$logger) {
        $this->logger = $logger;
        $this->logger->logmsg('Logging added to controller ' . get_class($this));
    }
    
    public function setSanitizer(&$sanitizer) {
        $this->sanitizer = $sanitizer;
    }
    
    public function setFormatter(&$formatter) {
        $this->formatter = $formatter;
        
    }
	
    /**
     * Get a reference to a baseModel implementation for this table
     *
     * @param string $table
     * @return tx_wildsidemvc_basemodel
     */
    public function getBaseModelFor($table) {
    	$this->log('Getting basemodel for: '.$table);
    	return $this->modelFactory->getModelByTable($table);
    }
    
    public function log($msg) {
        if(!is_null($this->logger)) {
            $bt = debug_backtrace();
            $methodName = $bt[1]['function'];
            $this->logger->logmsg(get_class($this) . '->' . $methodName . ': ' . $msg);
        }
    }
    
    /**
     * Extend this method in your controller, to filter all requests to a given controller, before the controller method is called. 
     * @param <type> $request
     * @return <type>
     */
    protected function preFilter($request) {
        return $request;
    }
    
    /**
     * Entrypoint for the basecontroller, the handle-method is called by the requestHandler - this method then 
     * parses the request, and delegates it to the correct method - or returns a Controller method not found message. 
     * @param <type> $request
     * @return <type>
     */
    public function handle(&$request) {
        $result = '';
        $request = $this->preFilter($request);
        if($request['subcmd']!=""){
            $handleMethod = 'if (method_exists($this, $request[\'subcmd\'])) {$result = $this->'.$request['subcmd'].'($request);} else {$result = \'Controller method not found\';  }';
            eval($handleMethod);
        }
        $result = $this->postFilter($request, $result);
        return $result;
    }
    
    /**
     * Extend this method in your controller, to filter all requests to a given controller, after the controller method is called. 
     * @param <type> $request
     * @param <type> $result
     * @return <type>
     */
    protected function postFilter($request, $result) {
        return $result;
    }
    
    
    /**
     * A debug function for testing if the controller works - can of course be overridden (for example to not return anything). 
     * @param <type> $request
     * @return <type>
     */
    public function test($request) {
        return "TEST OK";
    }
    
    
    protected function isUserLoggedIn() {
        return $GLOBALS["TSFE"]->loginUser;
    }
    
    protected function getLoggedInUserInfo() {
        if(!$this->isUserLoggedIn()) {
            return null;
        }
        return $GLOBALS["TSFE"]->fe_user->user;
    }
   
    /**
     * Creates a marker array from a datarow. 
     * @param <type> $row
     * @return <type>
     */
    public function getMarkerArray($row) {
        $markerArray = array();
        foreach($row as $key=>$value) {
            $markerArray['###'.strtoupper($key).'###'] = $value;
        }
        return $markerArray;
    }
    
    /**
     * Parses a template and inserts each datarow
     * @param <string> $template      The template
     * @param <string> $insertMarker  The marker where the complete list of templatet rows will be inserted
     * @param <string> $rowMarker     The marker which gets the template for each row
     * @param <datarows> $rows        An array of datarows
     * @return <type>
     */
    public function parseTemplate($template, $insertMarker, $rowMarker, $rows) {
        $content = '';
        foreach($rows as $row) {
            $markerArray = $this->getMarkerArray($row);
            $view = $this->cObj->getSubpart($template,$rowMarker);
            $content .= $this->cObj->substituteMarkerArrayCached($view,$markerArray);
        }
        $markerArray = array();
        $markerArray[$insertMarker] = $content;
        $result = $this->cObj->substituteMarkerArrayCached($template,$markerArray);
        return $result;
    }
    
    /**
     * Method for calculating the viewPath from the extensionkey and the classname of the calling class. 
     * @return <type>
     */
    protected function getViewPath() {
        $viewPath = str_replace('tx_'.$this->extkey.'_', '', $this->controller);
        $viewPath = str_replace('Controller', '', $viewPath);
        $viewPath = 'EXT:'.$this->extkey.'/views/'.$viewPath.'/';
        return $viewPath;
    }
    
    /**
     * Method for getting the view, based on the viewPath and methodName. On top of that, the filetype can be decided 
     * as well as a valid subpath. 
     * 
     * If no valid methodName is sent, the methodname of the caller is used. 
     * @param <type> $methodName
     * @param <type> $type
     * @param <type> $subpath
     * @return <type>
     */
    protected function getViewFileName($methodName="", $type=".html", $subpath="") {
      if($methodName=="") {
        $bt = debug_backtrace();
        $methodName = $bt[1]['function'];
      }
      
      $filename = $this->getViewPath().$subpath.$methodName.$type;
      $view = $this->cObj->fileResource($filename);
      return $view;
      
    }
    
    /**
     * All controllers has a default method called main, which returns nothing. 
     * @param <type> $request
     * @return <type>
     */
    protected function main($request) {
        return "";
    }
    
    /**
     * Public medthod for getting a view (no subdir, and filetype is html)
     * 
     * If no valid methodName is sent, the methodname of the caller is used. 
     * @param <type> $methodName
     * @return <type>
     */
    public function getView($methodName="") {
      if($methodName=="") {
        $bt = debug_backtrace();
        $methodName = $bt[1]['function'];
      }
      return $this->getViewFileName($methodName, '.html');  
    }
    
    /**
     * Public medthod for getting the special headerData view (no subdir, and filetype is inc)
     * Use this method to return viewdata for the head-section of the html page. 
     * 
     * If no valid methodName is sent, the methodname of the caller is used. 
     * @param <type> $methodName
     * @return <type>
     */    
    public function getHeaderData($methodName="") {
      if($methodName=="") {
        $bt = debug_backtrace();
        $methodName = $bt[1]['function'];
      }
        return $this->getViewFileName($methodName, '.inc');
    }
    
    /**
     * Public medthod for getting a js view (js/ subdir, and filetype is js). 
     * Use this method to return javascript files. 
     * 
     * If no valid methodName is sent, the methodname of the caller is used. 
     * @param <type> $methodName
     * @return <type>
     */
    public function getJsFile($methodName="") {
      if($methodName=="") {
        $bt = debug_backtrace();
        $methodName = $bt[1]['function'];
      }
        return $this->getViewFileName($methodName, '.js', 'js/');
    }
    
    /**
     * Public medthod for getting a css view (css/ subdir, and filetype is css)
     * Use this method to return stylesheet files. 
     * 
     * If no valid methodName is sent, the methodname of the caller is used. 
     * @param <type> $methodName
     * @return <type>
     */
    public function getCssFile($methodName="") {
      if($methodName=="") {
        $bt = debug_backtrace();
        $methodName = $bt[1]['function'];
      }
        return $this->getViewFileName($methodName, '.css', 'css/');
    }
    
    protected function handleUpload($request){
        if($_GET['cmd']=="handleUpload"||$_GET['subcmd']=="handleUpload"||$_POST['cmd']=="handleUpload"||$_POST['subcmd']=="handleUpload") {
            //Disallow calling this method directly from eid script. 
            //If eid access is needed the subclass needs to call this method explicitly
            //EXAMPLE: 
            //$reqeust['uploadFileIdentifier'] = 'formfieldname';
            //$uploadedFileName = $this->handleUpload($request);
            
            //Remember also to check the return value for an error message
            return "Not allowed";
        }
        $uploaddir = $request['filepath'];
        $uploadfile = $uploaddir.$_FILES[$request['uploadFileIdentifier']]['name'];
        
        if(is_file($uploadfile)&&$request['allowoverwrite']!="true"){//file already exists?
          return "FILE EXISTS";
        }
        
        if(move_uploaded_file($_FILES[$request['uploadFileIdentifier']]['tmp_name'], $uploadfile)) {//succes!
            $filemode = octdec($this->conf['fileMode']);
            @chmod($uploadfile,$filemode);
         } else {
            return "UPLOAD FAILED";
        }
        //Return the filename
        return $uploadfile;
    }
    
    private function mime_allowed($mime){
        if(!($this->conf['checkMime'])) return TRUE;         //all mimetypes allowed
        //TODO: Needs refactoring - since typoscript configuration is not allowed of private methods in the mvc framework. 
        $includelist = explode(",",$this->conf['mimeInclude']);
        $excludelist = explode(",",$this->conf['mimeExclude']);        //overrides includelist
        return (   (in_array($mime,$includelist) || in_array('*',$includelist))   &&   (!in_array($mime,$excludelist))  );
    }

    private function ext_allowed($filename){
        if(!($this->conf['checkExt'])) return TRUE;            //all extensions allowed
        $includelist = explode(",",$this->conf['extInclude']);
        $excludelist = explode(",",$this->conf['extExclude'])     ;    //overrides includelist
        $extension='';
        if($extension=strstr($filename,'.')){
            $extension=substr($extension, 1);    
            return ((in_array($extension,$includelist) || in_array('*',$includelist)) && (!in_array($extension,$excludelist)));
        } else {
            return FALSE;
        }
    }
    /**
     * Every controller can call another controllers action with this method. Do not call local controller methods through this method, since local methods are better called through $this->action - this indirect method has two much overhead for that. 
     * @param <type> $controller    The controller name (not the class name but the actual controller name - no tx_extensionkey prefix and no Controller postfix)
     * @param <type> $action        The action name (the actual name of the method in the controller) 
     * @param <type> $request       The current request object
     * @return <type>
     */
    protected function callController($controller, $action, $request) {
        $requestHandler = t3lib_div::makeInstance('tx_wildsidemvc_requesthandler'); 
        $requestHandler->setParentExtension($this->extkey);
        $request['cmd'] = $controller;
        $request['subcmd'] = $action;
        return $requestHandler->handle($request);
    }
    
    /**
     * Get an array for image resizing
     * @param <type> $url         Local path (relative path to file from the baseurl of the site) 
     * @param <type> $maxwidth    Max width (optional)
     * @param <type> $maxheight   Max height (optional) 
     * @return <type>
     */
    protected function getResizeImageData($url='', $maxwidth='0', $maxheight='0') {
        $this->log('URL for thumb: ' . $url);
        $this->log('Thumb width: ' . $width);
        $conf  = array(
           'file' => $url,
           'file.' => array(
           ),
        );
        if($maxwidth!="0") {
            $conf['file.']['width'] = $maxwidth.'m';
        }
        if($maxheight!="0") {
            $conf['file.']['height'] = $maxheight.'m';
        }
        return $conf;
     }
     
    /**
     * Get the filename for a resized image file. 
     * @param <type> $conf        Array containing size information and filename
     * @return <type>
     */
     protected function getResizedImageUrl($conf) {
         $fileName = $this->cObj->IMG_RESOURCE($conf);
         return $fileName;
     }
     
     /**
      * Get the filename inside an image tag for a resized image file. 
      * @param <type> $conf        Array containing size information and filename
      * @return <type>
      */
     protected function getResizedImageTag($conf) {
         $tag = $this->cObj->IMAGE($conf);
         return $tag;
     }
     
     protected function combineJs($filenames) {
         $view = ''; 
         foreach($filenames as $filename) {
              $view .= $this->getJsFile($filename)."\n";
          }
        return $view;
     }
     
     protected function combineCss($filenames) {
         $view = ''; 
         foreach($filenames as $filename) {
              $view .= $this->getCssFile($filename)."\n";
          }
        return $view;
     }
     
     protected function getJsFileList() {
        $rArray = array();
        $path = $this->getViewPath().'js/';
        $path = str_replace('EXT:', 'typo3conf/ext/', $path);
        $dh = opendir(PATH_site.$path);
        while (false !== ($file = readdir($dh))) {
        //Don't list subdirectories
        if (!is_dir("$dirpath/$file")) {
          $file = str_replace('.js', '', $file);
          $rArray[] = $file;
        }
        }
        closedir($dh); 
        sort($rArray);
        return $rArray;
     }
     
     protected function getCssFileList() {
        $rArray = array();
        $path = $this->getViewPath().'css/';
        $path = str_replace('EXT:', 'typo3conf/ext/', $path);
        $dh = opendir(PATH_site.$path);
        while (false !== ($file = readdir($dh))) {
        //Don't list subdirectories
        if (!is_dir("$dirpath/$file")) {
          $file = str_replace('.css', '', $file);
          $rArray[] = $file;
        }
        }
        closedir($dh); 
        sort($rArray);
        return $rArray;
     }
}
?>