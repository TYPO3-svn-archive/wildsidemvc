<?php
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_iconfigurationmanager.php');
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_xml.php');
class tx_wildsidemvc_configurationmanager implements tx_wildsidemvc_iconfigurationmanager {
    var $extkey;
    var $models;
    var $xml;
    public function __construct($extkey) {
      $this->extkey = $extkey;
      $request_url =

      $xml = new tx_wildsidemvc_xml(t3lib_extMgm::extPath($this->extkey).'configuration.xml');
      $xpathElement = "/models/*";
      $this->models = $xml->$xpathElement;
    }

    public function getConfigurationArray() {
        return $this->models;
    }
}
?>