<?php
/**
 * @file
 *
 */

/**
 *  select class which is a child of step
 *
 *  The class contains four methods:
 *  - __construct
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js()
 */

class select extends step {
    
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

    if ((substr($intent,0,7)=="select ") or (substr($intent,0,7)=="choose ")){
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
    $param1 = trim(substr($params,0,strpos($params," as "))); 
    $param2 = trim(substr($params,4+strpos($params," as ")));

    if (is_sikuli($param1) and is_sikuli($param2)) {
      $abs_param1 = abs_file($param1); $abs_intent = str_replace($param1,$abs_param1,$raw_intent);
      $abs_param2 = abs_file($param2); $abs_intent = str_replace($param2,$abs_param2,$abs_intent);
      return call_sikuli($abs_intent,$abs_param1);
    } // use sikuli visual automation as needed

    if (($param1 == "") or ($param2 == "")) echo "ERROR - " . current_line() . " target/option missing for " . $raw_intent . "\n";

    else return "{techo('".$raw_intent."');".beg_tx($param1)."var select_locator = tx('".$param1."');\n"."if (is_xpath_selector(select_locator.toString().replace('xpath selector: ','')))\n"."select_locator = select_locator.toString().substring(16);\n".$twb.".selectOptionByValue(select_locator,'".$param2."');".end_tx($param1);
  }


  
  public function get_header_js() {
    $js = <<<TAGUI
function select_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
var param1 = (params.substr(0,params.indexOf(' as '))).trim();
var param2 = (params.substr(4+params.indexOf(' as '))).trim();
if (is_sikuli(param1) && is_sikuli(param2)) {
var abs_param1 = abs_file(param1); var abs_intent = raw_intent.replace(param1,abs_param1);
var abs_param2 = abs_file(param2); abs_intent = abs_intent.replace(param2,abs_param2);
return call_sikuli(abs_intent,abs_param1);} // use sikuli visual automation as needed
if ((param1 == '') || (param2 == '')) return "this.echo('ERROR - target/option missing for " + raw_intent + "')";
else if (check_tx(param1)) return "var select_locator = tx('" + param1 + "'); if (is_xpath_selector(select_locator.toString().replace('xpath selector: ',''))) select_locator = select_locator.toString().substring(16); this.selectOptionByValue(select_locator,'" + param2 + "');";
else return "this.echo('ERROR - cannot find " + param1 + "')";}
TAGUI;
    return $js;
  }    


}

class_alias('select', 'choose');
