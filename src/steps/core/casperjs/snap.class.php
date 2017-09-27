<?php
/**
 * @file
 *
 */

/**
 *  snap class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
 */

class snap extends step {  
    
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

    if (substr($intent,0,5)=="snap "){
      return $this->intent;
    }    
    return FALSE;
  }

  /**
   * @return string 
   *   casperjs code as string
   *
   * @param string $raw_intent
   *   The full written step line for passing directly to the casperjs output or parsing for sikuli
   * @param array $params
   *   Array of params for the given step
   * @param string $twb
   *   Tagui_web_browser token for constructing test header casperjs   
   * @param boolean $sikuli
   *   if input is meant for sikuli visual automation 
   *
   */
  public function parseIntent($intent, $raw_intent, $twb, $sikuli=FALSE) {     
    // TODO: $params is passed as an array but sent to casperjs code and sikuli output as a string   
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params," to "))); 
    $param2 = trim(substr($params,4+strpos($params," to ")));


    if ((strtolower($params) == "page") or (strtolower($param1) == "page")) {
      if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');\n".$twb.".capture('".abs_file($param2)."');}".end_fi()."\n";
      else return "{techo('".$raw_intent."');\n".$twb.".capture(snap_image());}".end_fi()."\n";
    }
    if ($params == "") echo "ERROR - " . current_line() . " target missing for " . $raw_intent . "\n";
    else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');".beg_tx($param1). $twb.".captureSelector('".abs_file($param2)."',tx('".$param1."'));".end_tx($param1); 
    else return "{techo('".$raw_intent."');".beg_tx($params). $twb.".captureSelector(snap_image(),tx('".$params."'));".end_tx($params);
  }

  public function get_header_js() {
    $js = <<<TAGUI
function snap_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
var param1 = (params.substr(0,params.indexOf(' to '))).trim();
var param2 = (params.substr(4+params.indexOf(' to '))).trim();
if ((params.toLowerCase() == 'page') || (param1.toLowerCase() == 'page')) {
if (params.indexOf(' to ') > -1) return "this.capture('" + abs_file(param2) + "')";
else return "this.capture(snap_image())";}
if (params == '') return "this.echo('ERROR - target missing for " + raw_intent + "')";
else if (params.indexOf(' to ') > -1)
{if (check_tx(param1)) return "this.captureSelector('" + abs_file(param2) + "',tx('" + param1 + "'))"; 
else return "this.echo('ERROR - cannot find " + param1 + "')";}
else {if (check_tx(params)) return "this.captureSelector(snap_image(),tx('" + params + "'))";
else return "this.echo('ERROR - cannot find " + params + "')";}}
TAGUI;
    return $js;
  }    

}
