<?php
/**
 * @file
 *
 */

/**
 *  receive class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js() 
 */

class receive extends step {  
  
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

    if (substr($intent,0,8)=="receive "){
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
    if (($param1 == "") or ($param2 == "")) echo "ERROR - " . current_line() . " keyword/filename missing for " . $raw_intent . "\n"; 
    else return "{techo('".$raw_intent."');\n"."casper.on('resource.received', function(resource) {if (resource.stage !== 'end') return;\n"."if (resource.url.indexOf('".$param1."') > -1) ".$twb.".download(resource.url, '".abs_file($param2)."');});}".end_fi()."\n";
  }

  public function getHeaderJs() {
    $js = <<<TAGUI
function receive_intent(raw_intent) {
return "this.echo('ERROR - step not supported in live mode, it requires creating CasperJS event')";}
TAGUI;
    return $js;
  }
  
}
