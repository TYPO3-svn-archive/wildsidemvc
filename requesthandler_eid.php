<?php
//SAMPLE EID SCRIPT. 
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_requesthandler.php');
tslib_eidtools::connectDB();
tslib_eidtools::initFeUser();

require_once(PATH_tslib.'class.tslib_fe.php');
require_once(PATH_t3lib.'class.t3lib_page.php');
$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
$GLOBALS['TSFE'] = new $temp_TSFEclassName($TYPO3_CONF_VARS, $pid, 0, true);
$GLOBALS['TSFE']->connectToDB();
$GLOBALS['TSFE']->initFEuser();
$GLOBALS['TSFE']->determineId();
$GLOBALS['TSFE']->getCompressedTCarray();
$GLOBALS['TSFE']->initTemplate();
$GLOBALS['TSFE']->getConfigArray();

$requestobject = array_merge($_SERVER, $_GET, $_POST);

$requestHandler = t3lib_div::makeInstance('tx_wildsidemvc_requesthandler');
$requestHandler->setParentExtension('wildsidemvc');
echo $requestHandler->handle($requestobject);
?>