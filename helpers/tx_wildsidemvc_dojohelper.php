<?php
class tx_wildsidemvc_dojohelper {
    var $defaultTheme = 'tundra';
    var $basePath = '';
    
    public function __construct($basepath) {
        $this->setBasePath($basepath);
    }
    
    public function setDefaultTheme($themeName) {
        $this->defaultTheme = $themeName;
    }
    
    public function setBasePath($path) {
        $this->basePath = $path;
    }
    
    public function includeDojo($debug="false", $parseOnLoad="true", $usePlainJsons="true") {
        $additionalAttributes = 'djConfig="isDebug: '.$debug.', parseOnLoad: '.$parseOnLoad.', usePlainJson:'.$usePlainJsons.'"';
        return $this->includeCustomScript($this-basePath.'dojo/dojo.js', $additionalAttributes);
    }
    
    public function includeCustomScript($scriptpath, $additionalAttributes="") {
        return '<script type="text/javascript" src="'.$scriptpath.'" '.$additionalAttributes.'></script>';
    }
    
    public function includeDojoCss($rel, $type, $media) {
        return $this->includeCustomCss($this-basePath.'dojo/resources/dojo.css', $rel, $type, $media);
    }   
    
    public function includeDijitTheme($themename, $rel, $type, $media) {
        return $this->includeCustomCss($this-basePath.'dijit/themes/'.$themename.'/'.$themename.'.css', $rel, $type, $media);
    }
    
    public function includeDefaultTheme() {
        return $this->includeDijitTheme($this->defaultTheme, $rel, $type, $media);
    }
    
    public function setIncludedStyleTag() {
        return '<style type="text/css" id="generatedStyles"></style>';
    }
       
    public function includeCustomCss($cssPath, $rel="stylesheet", $type="text/css", $media="screen") {
        return '<link href="'.$cssPath.'" rel="'.$rel.'" type="'.$type.'" media="'.$media.'" />';
    }
    
    public function getDataStore($type, $jsId, $url, $requestMethod) {
        return '<div dojoType="'.$type.'" jsId="'.$jsId.'" url="'.$url.'" requestMethod="'.$requestMethod.'"></div>';
    }
    
    public function getMultiSelect($name, $id, $pagesize="15", $searchAttr="name", $multiple="true", $style="") {
        return '<select name="'.$name.'" dojoType="dijit.form.MultiSelect" pageSize="'.$pagesize.'" searchAttr="'.$searchAttr.'" id="'.$id.'" multiple="'.$multiple.'" style="'.$style.'"></select>';
    }
    
    public function getDateTextBox($name, $id, $value="", $min='', $max='') {
        return '<input id="'.$id.'" type="text" name="'.$name.'" value="'.$value.'" dojoType="dijit.form.DateTextBox" constraints="{min:\'2008-07-01\',max:\'2010-12-31\'}" />';
    }
    
    public function getFilteringSelect($name, $id, $dataStore, $pagesize="15", $searchAttr="name", $autocomplete="true", $cssClass="") {
        return '<select dojoType="dijit.form.FilteringSelect" autoComplete="'.$autocomplete.'"  store="'.$dataStore.'" pageSize="'.$pagesize.'" name="'.$name.'" id="'.$id.'" class="'.$cssClass.'"></select>';
    }
    
    public function getValidationTextBox($name, $id, $value="", $trim="true", $required="true", $invalidMessage="Invalid value", $regExpGen="") {
        return '<input id="'.$id.'" type="text" name="'.$name.'" value="'.$value.'" class="fe_enquiry_textinput_wide" dojoType="dijit.form.ValidationTextBox" regExpGen="'.$regExpGen.'" trim="'.$trim.'" required="'.$required.'" invalidMessage="'.$invalidMessage.'" />';
    }
    
    public function getEmailValidationTextBox($name, $id, $value="", $trim="true", $required="true", $invalidMessage="Invalid Email Address") {
        return $this->getValidationTextBox($name, $id, $value, 'dojox.regexp.emailAddress');
    }
    
    
}
?>
