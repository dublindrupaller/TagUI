<?php
/**
 * @file
 *
 */

/**
 *  save class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js() 
 */

class save extends step {  
    
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

    if (substr($intent,0,5)=="save ") {
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
    $param1 = trim(substr($params,0,strpos($params," to "))); $param2 = trim(substr($params,4+strpos($params," to ")));
    if ((strtolower($params) == "page") or (strtolower($param1) == "page")) {
      if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');\nsave_text('".abs_file($param2)."',".$twb.".getHTML());}".end_fi()."\n";
      else return "{techo('".$raw_intent."');\nsave_text('',".$twb.".getHTML());}".end_fi()."\n"; 
    }
    if ($params == "") echo "ERROR - " . current_line() . " target missing for " . $raw_intent . "\n";
    else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');".beg_tx($param1). "save_text('".abs_file($param2)."',".$twb.".fetchText(tx('".$param1."')).trim());".end_tx($param1); 
    else return "{techo('".$raw_intent."');".beg_tx($params). "save_text('',".$twb.".fetchText(tx('" . $params . "')).trim());".end_tx($params);
  }

  public function getHeaderJs() {
    $js = <<<TAGUI
function save_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
var param1 = (params.substr(0,params.indexOf(' to '))).trim();
var param2 = (params.substr(4+params.indexOf(' to '))).trim();
if ((params.toLowerCase() == 'page') || (param1.toLowerCase() == 'page')) {
if (params.indexOf(' to ') > -1) return "save_text('" + abs_file(param2) + "',this.getHTML())";
else return "save_text('',this.getHTML())";}
if (params == '') return "this.echo('ERROR - target missing for " + raw_intent + "')";
else if (params.indexOf(' to ') > -1)
{if (check_tx(param1)) return "save_text('" + abs_file(param2) + "',this.fetchText(tx('" + param1 + "')).trim())";
else return "this.echo('ERROR - cannot find " + param1 + "')";}
else {if (check_tx(params)) return "save_text('',this.fetchText(tx('" + params + "')).trim())";
else return "this.echo('ERROR - cannot find " + params + "')";}}
TAGUI;
    return $js;
  }    
}
