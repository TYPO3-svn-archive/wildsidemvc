<?php

/**
* Class to read XML file
* Class use SimpleXml - default PHP class
* @author Michal Palma <palmic at centrum dot cz>
* @copyleft (l) 2005  Michal Palma
* @package Xml2Array
* @version 1.0
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @date 2005-07-29
*/
class tx_wildsidemvc_xml
{

    //== Attributes ======================================================================

    /**
    * store a name of file
    * @type string
    */
    private $filename;

    /**
    * store a file handler
    * @type resource
    */
    private $handler;


    //== constructors ====================================================================

    public function __construct(/*string*/ $filename)
    {
        if (!is_string($filename)) {
            throw new Exception("Bad parameter filename. Must be string.");
        }
        $this->filename = str_replace("\\", "/", $filename);
    }


    //== destructors ====================================================================
    //== public functions ===============================================================

    /**
    * returns content of XML element
    * @return array
    */
    public function __get($xpath)
    {
        if (strlen($xpath) < 1) {
            throw new Exception("Bad parameter XPATH. Must be string.");
        }
        try {
            if (!$resarray = $this->getHandler()->xpath($xpath)) {
                throw new Exception("Cannot read from file '". $this->filename ."'.\nXPATH = ". $xpath, 1);
            }
            foreach ($resarray as $resname => $resval) {
                $valarray[$resname] = $this->sxml2Array($resval);
            }
        }
        catch (Exception $e) {
            throw $e;
        }
        return $valarray;
    }


    //== protected functions =============================================================

    /**
    * returns array with values of given simpleXml objekt
    * @return array
    */
    protected function sxml2Array(SimpleXMLElement $sxmlo)
    {
        $values = ((array) $sxmlo);
        foreach ($values as $index => $value) {
            if (!is_string($value)) {
                $values[$index] = $this->sxml2Array($value);
            }
            else {
                $values[$index] = $value;
            }
        }
        return $values;
    }

    /**
    * open file
    * @return void
    */
    protected function open()
    {
        if (!is_a($this->handler, "SimpleXMLElement")) {
            $this->handler = @simplexml_load_file($this->filename);
        }
        if (!is_a($this->handler, "SimpleXMLElement")) {
            throw new Exception("Cannot open file. ". $this->filename, -1);
        }
    }

    /**
    * Getter of XML file handler
    * @return SimpleXMLElement
    */
    protected function getHandler() {
        try {
            $this->open();
        }
        catch (Exception $e) {
            throw $e;
        }
        return $this->handler;
    }
}




?>
