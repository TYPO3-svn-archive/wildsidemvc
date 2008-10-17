<?php
class tx_wildsidemvc_sanitizeHelper {
    public function sanitize_int($integer, $min='', $max='')
    {
      $int = intval($integer);
      if((($min != '') && ($int < $min)) || (($max != '') && ($int > $max)))
        return FALSE;
      return $int;
    }

    // sanitize a string for HTML (make sure nothing gets interpretted!)
    public function sanitize_html_string($string)
    {
      $pattern[0] = '/\&/';
      $pattern[1] = '/</';
      $pattern[2] = "/>/";
      $pattern[3] = '/\n/';
      $pattern[4] = '/"/';
      $pattern[5] = "/'/";
      $pattern[6] = "/%/";
      $pattern[7] = '/\(/';
      $pattern[8] = '/\)/';
      $pattern[9] = '/\+/';
      $pattern[10] = '/-/';
      $replacement[0] = '&amp;';
      $replacement[1] = '&lt;';
      $replacement[2] = '&gt;';
      $replacement[3] = '<br>';
      $replacement[4] = '&quot;';
      $replacement[5] = '&#39;';
      $replacement[6] = '&#37;';
      $replacement[7] = '&#40;';
      $replacement[8] = '&#41;';
      $replacement[9] = '&#43;';
      $replacement[10] = '&#45;';
      return preg_replace($pattern, $replacement, $string);
    }

    // sanitize a string for SQL input (simple slash out quotes and slashes)
    public function sanitize_sql_string($string, $min='', $max='')
    {
      $pattern[0] = '/(\\\\)/';
      $pattern[1] = "/\"/";
      $pattern[2] = "/'/";
      $replacement[0] = '\\\\\\';
      $replacement[1] = '\"';
      $replacement[2] = "\\'";
      $len = strlen($string);
      if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max)))
        return FALSE;
      return preg_replace($pattern, $replacement, $string);
    }

    
}  
    
?>