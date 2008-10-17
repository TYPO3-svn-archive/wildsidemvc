<?php
require_once('typo3conf/ext/wildsidemvc/tx_wildsidemvc_ilogger.php');
class tx_wildsidemvc_log implements tx_wildsidemvc_ilogger {
var $extkey;
    public function __construct($extkey) {
      $this->extkey = $extkey;
      $this->logmsg("\n\n\n");
    }
public function log($request) {
    $time = date("F jS Y, h:iA");
    $ip = $request['REMOTE_ADDR'];
    $browser = $request['HTTP_USER_AGENT'];
    $qstring = $request['QUERY_STRING'];
    $controller = $request['cmd'];
    $action = $request['subcmd'];
    $fp = fopen(t3lib_extMgm::extPath($this->extkey)."log.txt",  "a");
    fputs($fp, $time . ' ' . $ip . ' ' . $browser . ' ' . $controller . '->'.$action.' ('. $qstring . ')'."\n");
    fclose($fp);
}

public function logmsg($msg) {
    $time = date("F jS Y, h:iA");
    $fp = fopen(t3lib_extMgm::extPath($this->extkey)."log.txt",  "a");
    fputs($fp, $time . ' ' . $msg."\n");
    fclose($fp);
}

}
?>