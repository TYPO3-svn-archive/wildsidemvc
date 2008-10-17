<?php
interface tx_wildsidemvc_ibasemodel {
    function getFromId($uid);
    function getFromIdList($uidList);
    function getAll($where="", $order="", $limit="");
    function getFromRelation($field, $value, $orderBy="", $limit="", $addWhere = "");
    function getSingle($sql);
    function getList($sql);
}
?>