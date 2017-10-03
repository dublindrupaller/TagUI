<?php
/**
 * @file
 *
 */
/**
 *  load class which is a child of step
 *  The class contains four methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js()
 */
class load extends step {
  
  /**
   * Construct wrapper object   
   */
  public function __construct($intent){    
    $this->intent = $intent;
  }
  /**
   * @return string
   */
  public function getIntent($intent) {    
    
    if (substr($intent,0,5)=="load ") {
      return $this->intent;
   }
    return FALSE;
  }
  /**
   * @return string 
   *   casperjs code as string
   *
   * @param array $params
   *   Array of params for the given step
   * @param string $twb
   *   Tagui_web_browser token for constructing test header casperjs   
   *
   */
  public function parseIntent($intent, $raw_intent, $twb, $sikuli=FALSE) {     
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params," as "))); $param2 = trim(substr($params,4+strpos($params," as ")));
    if (($param1 == "") or ($param2 == ""))
    echo "ERROR - " . current_line() . " filename missing for " . $raw_intent . "\n"; else
    return "{techo('".$raw_intent."');".beg_tx($param1).
    $twb.".page.uploadFile(tx('".$param1."'),'".abs_file($param2)."');".end_tx($param1);
  }
  


  public function getHeaderJs() {
    $js = <<<TAGUI
function load_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
var param1 = (params.substr(0,params.indexOf(' to '))).trim();
var param2 = (params.substr(4+params.indexOf(' to '))).trim();
if (params == '') return "this.echo('ERROR - filename missing for " + raw_intent + "')";
else if (params.indexOf(' to ') > -1)
return "var fs = require('fs'); " + param2 + " = ''; if (fs.exists('" + abs_file(param1) + "')) " + param2 +  " = fs.read('" + abs_file(param1) + "').trim(); else this.echo('ERROR - cannot find file " + param1 + "')"; else
return "this.echo('ERROR - variable missing for " + raw_intent + "')";}
TAGUI;
    return $js;
  }      
} 
