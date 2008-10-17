<?php
class tx_wildsidemvc_dataobject_arrayhandler {
    /**
     * 
     * @param <string> $fieldname           Fieldname of the dataobject-field to filter upon. 
     * @param <object> $value               Value to filter
     * @param <object> $dataObjectArray     Dataobject array to filter
     * @return <object>                     The filtered object array
     */
    public function filter($fieldname, $value, $dataObjectArray) {
        $returnObj = array();
        $i = 0;
        foreach($dataObjectArray as $dataObject) {
            if($dataObject[$fieldname]==$value) {
                $returnObj[$i] = $dataObject;
                $i++;
            }
        }
        return $returnObj;
    }
}
?>