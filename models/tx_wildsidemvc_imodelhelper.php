<?php
interface tx_wildsidemvc_imodelhelper {
    function getStringFields();
    function getOrderBy($orderField, $direction="ASC");
    function sortingAsc();
    function sortingDesc();
    function getLimit($number, $start=0);
    function getPage($page=0, $numberPrPage=10);
    function getWhereNumericField($fieldName, $value=0, $equalSign="=", $prependWith="AND");
    function getWhereNumericRange($fieldName, $valueStart=0, $valueEnd=0, $prependWith="", $parantesis=false);
}
?>
