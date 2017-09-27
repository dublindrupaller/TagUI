<?php
/**
 * @file
 *
 */

/**
 *  write class which is a child of step
 *
 *  The class contains four methods:
 *  - __construct
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js()
 */

class write extends step {  
      
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

    if (substr($intent, 0, 6) == "write " || $intent == "write") {
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
   *
   */
  public function parseIntent($intent, $raw_intent, $twb, $sikuli=FALSE) {     
    // TODO: $params is passed as an array but sent to casperjs code and sikuli output as a string   
    $raw_intent = str_replace("'","\"",$raw_intent); // avoid breaking echo below when single quote is used
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params," to "))); 
    $param2 = trim(substr($params,4+strpos($params," to ")));
    if ($params == "") echo "ERROR - " . current_line() . " variable missing for " . $raw_intent . "\n";
    else if (strpos($params," to ")!==false) return "{techo('".$raw_intent."');\nappend_text('".abs_file($param2)."',".add_concat($param1).");}".end_fi()."\n";
    else return "{techo('".$raw_intent."');\nappend_text(''," . add_concat($params) . ");}".end_fi()."\n";
  }

  
    
  public function get_header_js() {
    $js = <<<TAGUI
function write_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
var param1 = (params.substr(0,params.indexOf(' to '))).trim();
var param2 = (params.substr(4+params.indexOf(' to '))).trim();
if (params == '') return "this.echo('ERROR - variable missing for " + raw_intent + "')";
else if (params.indexOf(' to ') > -1)
return "append_text('" + abs_file(param2) + "'," + add_concat(param1) + ")"; else
return "append_text(''," + add_concat(params) + ")";}
TAGUI;
    return $js;
  }      
}
