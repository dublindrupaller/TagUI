<?php
/**
 * @file
 *
 */
/**
 *  url class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js() 
 */
class url extends step {
  
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
    
    if ((substr($intent,0,7)=="http://") || (substr($intent,0,8)=="https://")) {
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
    $casper_url = $raw_intent; $chrome_call = '';
    
    if ($twb == 'chrome') {
      $casper_url = 'about:blank';
      $chrome_call = "chrome_step('Page.navigate',{url: '".$raw_intent."'});
      sleep(1000);\n";
    }
    
    if (strpos($raw_intent,"'+")!==false and strpos($raw_intent,"+'")!==false) {
      // check if dynamic url is used
      //  wrap step within casper context if variable (casper context) is used in url, in order to access variable
      $dynamic_header = "\n{casper.then(function() {\n"; $dynamic_footer = "})} // end of dynamic url block\n";
    }
    else {
      $dynamic_header = "";
      $dynamic_footer = ""; // else casper.start/thenOpen can be outside casper context
      if (filter_var($raw_intent, FILTER_VALIDATE_URL) == false) {
        // do url validation only for raw text url string
        echo "ERROR - " . current_line() . " invalid URL " . $raw_intent . "\n";
      }
      if ($GLOBALS['line_number'] == 1) {
        // use casper.start for first URL call and casper.thenOpen for subsequent calls
        $GLOBALS['url_provided']=true;
        return $dynamic_header."casper.start('".$casper_url."', function() {\n".$chrome_call.
        "techo('".$raw_intent."' + ' - ' + ".$twb.".getTitle() + '\\n');});\n\ncasper.then(function() {\n".$dynamic_footer;
    }
    else {
      return $dynamic_header."});casper.thenOpen('".$casper_url."', function() {\n".$chrome_call."techo('".
      $raw_intent."' + ' - ' + ".$twb.".getTitle());});\n\ncasper.then(function() {\n".$dynamic_footer;}
    }
  }

  
  public function getHeaderJs() {
    $js = <<<TAGUI
function url_intent(raw_intent) {
if (chrome_id == 0) return "this.echo('ERROR - step only supported in live mode using Chrome browser')";
else return "this.evaluate(function() {window.location.href = \"" + raw_intent + "\"})";}
TAGUI;
    return $js;
  }      
} 
