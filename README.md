# FORK DETAILS - unstable - work in progress
This is a working demo for Ken illustrating the ability to:

1. Allow the elegant addition of new steps to tagui without polluting or hacking core tagui files.
2. Increased performance using modular assembly & collation of tagui_header.js
3. Reduce repetition in core tagui codebase using Object Orientated approach to parsing natural language tests.
4. Allow the switch to using tagui to run casperjs, webdriver, Puppeteer (and potentially other) script language tests.

# Core Concepts  - OOP approach to parsing steps
Below is an example of the suggested approach. It uses an Object Oriented approach to building the step definitions and the resulting output.
```
# core TAGUI Steps are now located in the following folder
tagui/src/steps/core
# custom TAGUI Steps are placed in the following folder which is added to .gitignore
tagui/src/steps/custom
```
Logic in the tagui_parser.php file runs a lookup against these folders (using a naming pattern) and loads each core & custom step into memory (improving performance slightly).

```php
/**
 * @file
 *
 */

/**
 *  tap class which is a child of step
 *  The class contains three methods:
 *  - public getIntent()
 *  - public parseIntent()
 *  - public get_header_js() 
 */

class tap extends step {
      
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

    if ((substr($intent,0,4)=="tap ") || (substr($intent,0,6)=="click ")) {
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

  $params = trim(substr($raw_intent." ",1+strpos($raw_intent." "," ")));
  // TODO: $params is passed as an array but sent to casperjs code and sikuli output as a string   
    if ($sikuli) {
      $abs_params = abs_file($params); 
      $abs_intent = str_replace($params,$abs_params,$raw_intent);
      $parsed_code =  call_sikuli($abs_intent,$abs_params);
    } else {
      $parsed_code = "{techo('".$raw_intent."');".beg_tx($params).$twb.".click(tx('" . $params . "'));".end_tx($params);       
    }    
    return $parsed_code;
  } 

  public function getHeaderJs() {
    $js = <<<TAGUI
function tap_intent(raw_intent) {
var params = ((raw_intent + ' ').substr(1+(raw_intent + ' ').indexOf(' '))).trim();
if (is_sikuli(params)) {var abs_params = abs_file(params); var abs_intent = raw_intent.replace(params,abs_params);
return call_sikuli(abs_intent,abs_params);} 
if (params == '') return "this.echo('ERROR - target missing for " + raw_intent + "')";
else if (check_tx(params)) return "this.click(tx('" + params + "'))";
else return "this.echo('ERROR - cannot find " + params + "')";}
TAGUI;
    return $js;
  }       
}
class_alias('tap', 'click');
```

The key advantages of those approach are as follows:

**1. Simplifies enhancements and future-proofs contibutions**
As you can see from the above example step, you can tweak the core step intent output and the tagui_headerjs output in the one file. 

```php
getIntent() # works out the array of arguments for a given step and returns the necessary output for parsing
parseIntent() # returns the output script to be added to the test file for the given step
getHeaderJs() # returns the script to be added to the tagui_header.js (chrome live mode) for the given step
```
So, instead of touching and updating tagui_header.js and tagui_parse.php, you just update the step class file.

**2. Extend steps elegantly and benefit from further improvements**
As the usage of TAGUI increases, so does the opportunity for more collaborations and improvements that can be shared/merged with the core TAGUI codebase. The Object Orientated approach beging put forward here allows that. In other words, TAGUI users who want to tweak and extend TAGUI for their own use have, at the moment, no choice but to change tagui_parse.php, tagui_header.js along with other files. Which also means, if the core TAGUI code improves, they run into conflicts when trying to update their local working version.

**3. Future-proof TAGUI by supporting casperJS, webdriver, puppeteer and other test script languages**
The approach in this demo illustrates that you can use TAGUI to parse natural language test files into languages other than casperjs. 

```php
# default context is "casperjs" which means the new tagui_parse.php logic looks at the following folders
tagui/src/steps/core/casperjs
tagui/src/steps/custom/casperjs
# we can introduce other contexts such as "puppeteer" which means the new tagui_parse.php logic looks at the following folders
tagui/src/steps/core/puppeteer
tagui/src/steps/custom/puppeteer
# or webdriver for example
tagui/src/steps/core/webdriver
tagui/src/steps/custom/webdriver
```
Please note: I have just setup those folders for illustrative/demo purposes. 


