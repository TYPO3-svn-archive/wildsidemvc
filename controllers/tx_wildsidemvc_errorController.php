<?php
require_once('typo3conf/ext/wildsidemvc/controllers/tx_wildsidemvc_baseController.php');
class tx_wildsidemvc_errorController extends tx_wildsidemvc_baseController {
       
    public function handle($request) {
        return "ERROR: Controller not found!";
    }
}
?>