<?php
/**
 * @file
 *
 */

/**
 *  down class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public getHeaderJs()
 */

class down extends step {

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

   if ((substr($intent,0,5)=="down ") or (substr($intent,0,9)=="download ")) {
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
  public function parseIntent($intent, $raw_intent, $twb, $sikuli = FALSE) {     
    // TODO: $params is passed as an array but sent to casperjs code as a string    
    $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
    $param1 = trim(substr($params,0,strpos($params," to "))); 
    $param2 = trim(substr($params,4+strpos($params," to ")));

    if (($param1 == "") or ($param2 == "")) echo "ERROR - " . current_line() . " url/filename missing for " . $raw_intent . "\n"; 
    else return "{techo('".$raw_intent."');\n".$twb.".download('".$param1."','".abs_file($param2)."');}".end_fi()."\n";
  } 


  public function getHeaderJs() {
    $js = <<<TAGUI
function down_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
var param1 = (params.substr(0,params.indexOf(' to '))).trim();
var param2 = (params.substr(4+params.indexOf(' to '))).trim();
if ((param1 == '') || (param2 == '')) return "this.echo('ERROR - url/filename missing for " + raw_intent + "')";
else return "this.download('" + param1 + "','" + abs_file(param2) + "')";}
TAGUI;
   return $js;
  }


}
