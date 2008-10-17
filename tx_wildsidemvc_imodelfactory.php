<?php
interface tx_wildsidemvc_imodelfactory {
    public function getModelByName($modelName);
    public function getModelByTable($tableName);
}
?>
