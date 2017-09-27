<?php
/**
 * @file
 *
 */
/**
 *  load class which is a child of step
 *  The class contains two methods:
 *  - public getIntent()
 *  - public parseIntent()
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
    
    if (substr($lc_raw_intent,0,5)=="load ") {
      return $this->intent;
   }
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
    $twb.".page.uploadFile(tx('".$param1."'),'".abs_file($param2)."');".end_tx($param1);}
  }


  public function get_header_js() {
    $js = <<<TAGUI
function url_intent(raw_intent) {
if (chrome_id == 0) return "this.echo('ERROR - step only supported in live mode using Chrome browser')";
else return "this.evaluate(function() {window.location.href = \"" + raw_intent + "\"})";}
TAGUI;
    return $js;
  }      
}
