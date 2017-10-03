
// for live mode simple parsing of tagui steps into js code
function tagui_parse(raw_input) {return parse_intent(raw_input);}

// for live mode interpretation of step into casperjs code
function parse_intent(live_line) {
live_line = live_line.trim(); if (live_line == '') return '';
switch (get_intent(live_line)) {
case 'url': return url_intent(live_line); break;
case 'tap': return tap_intent(live_line); break;
case 'hover': return hover_intent(live_line); break;
case 'type': return type_intent(live_line); break;
case 'select': return select_intent(live_line); break;
case 'read': return read_intent(live_line); break;
case 'show': return show_intent(live_line); break;
case 'upload': return upload_intent(live_line); break;
case 'down': return down_intent(live_line); break;
case 'receive': return receive_intent(live_line); break;
case 'echo': return echo_intent(live_line); break;
case 'save': return save_intent(live_line); break;
case 'dump': return dump_intent(live_line); break;
case 'write': return write_intent(live_line); break;
case 'load': return load_intent(live_line); break;
case 'snap': return snap_intent(live_line); break;
case 'table': return table_intent(live_line); break;
case 'wait': return wait_intent(live_line); break;
case 'live': return live_intent(live_line); break;
case 'check': return check_intent(live_line); break;
case 'test': return test_intent(live_line); break;
case 'frame': return frame_intent(live_line); break;
case 'popup': return popup_intent(live_line); break;
case 'api': return api_intent(live_line); break;
case 'dom': return dom_intent(live_line); break;
case 'js': return js_intent(live_line); break;
case 'timeout': return timeout_intent(live_line); break;
case 'code': return code_intent(live_line); break;
default: return "this.echo('ERROR - cannot understand step " + live_line.replace(/'/g,'\\\'') + "')";}}

// for live mode understanding intent of line entered
function get_intent(raw_intent) {var lc_raw_intent = raw_intent.toLowerCase();
if (lc_raw_intent.substr(0,7) == 'http://' || lc_raw_intent.substr(0,8) == 'https://') return 'url';

// first set of conditions check for valid keywords with their parameters
if ((lc_raw_intent.substr(0,4) == 'tap ') || (lc_raw_intent.substr(0,6) == 'click ')) return 'tap';
if ((lc_raw_intent.substr(0,6) == 'hover ') || (lc_raw_intent.substr(0,5) == 'move ')) return 'hover';
if ((lc_raw_intent.substr(0,5) == 'type ') || (lc_raw_intent.substr(0,6) == 'enter ')) return 'type';
if ((lc_raw_intent.substr(0,7) == 'select ') || (lc_raw_intent.substr(0,7) == 'choose ')) return 'select';
if ((lc_raw_intent.substr(0,5) == 'read ') || (lc_raw_intent.substr(0,6) == 'fetch ')) return 'read';
if ((lc_raw_intent.substr(0,5) == 'show ') || (lc_raw_intent.substr(0,6) == 'print ')) return 'show';
if ((lc_raw_intent.substr(0,3) == 'up ') || (lc_raw_intent.substr(0,7) == 'upload ')) return 'upload';
if ((lc_raw_intent.substr(0,5) == 'down ') || (lc_raw_intent.substr(0,9) == 'download ')) return 'down';
if (lc_raw_intent.substr(0,8) == 'receive ') return 'receive';
if (lc_raw_intent.substr(0,5) == 'echo ') return 'echo';
if (lc_raw_intent.substr(0,5) == 'save ') return 'save';
if (lc_raw_intent.substr(0,5) == 'dump ') return 'dump';
if (lc_raw_intent.substr(0,6) == 'write ') return 'write';
if (lc_raw_intent.substr(0,5) == 'load ') return 'load';
if (lc_raw_intent.substr(0,5) == 'snap ') return 'snap';
if (lc_raw_intent.substr(0,6) == 'table ') return 'table';
if (lc_raw_intent.substr(0,5) == 'wait ') return 'wait';
if (lc_raw_intent.substr(0,5) == 'live ') return 'live';
if (lc_raw_intent.substr(0,6) == 'check ') return 'check';
if (lc_raw_intent.substr(0,5) == 'test ') return 'test';
if (lc_raw_intent.substr(0,6) == 'frame ') return 'frame';
if (lc_raw_intent.substr(0,6) == 'popup ') return 'popup';
if (lc_raw_intent.substr(0,4) == 'api ') return 'api';
if (lc_raw_intent.substr(0,4) == 'dom ') return 'dom';
if (lc_raw_intent.substr(0,3) == 'js ') return 'js';
if (lc_raw_intent.substr(0,8) == 'timeout ') return 'timeout';

// second set of conditions check for valid keywords with missing parameters
if ((lc_raw_intent == 'tap') || (lc_raw_intent == 'click')) return 'tap';
if ((lc_raw_intent == 'hover') || (lc_raw_intent == 'move')) return 'hover';
if ((lc_raw_intent == 'type') || (lc_raw_intent == 'enter')) return 'type';
if ((lc_raw_intent == 'select') || (lc_raw_intent == 'choose')) return 'select';
if ((lc_raw_intent == 'read') || (lc_raw_intent == 'fetch')) return 'read';
if ((lc_raw_intent == 'show') || (lc_raw_intent == 'print')) return 'show';
if ((lc_raw_intent == 'up') || (lc_raw_intent == 'upload')) return 'upload';
if ((lc_raw_intent == 'down') || (lc_raw_intent == 'download')) return 'down';
if (lc_raw_intent == 'receive') return 'receive';
if (lc_raw_intent == 'echo') return 'echo';
if (lc_raw_intent == 'save') return 'save';
if (lc_raw_intent == 'dump') return 'dump';
if (lc_raw_intent == 'write') return 'write';
if (lc_raw_intent == 'load') return 'load';
if (lc_raw_intent == 'snap') return 'snap';
if (lc_raw_intent == 'table') return 'table';
if (lc_raw_intent == 'wait') return 'wait';
if (lc_raw_intent == 'live') return 'live';
if (lc_raw_intent == 'check') return 'check';
if (lc_raw_intent == 'test') return 'test';
if (lc_raw_intent == 'frame') return 'frame';
if (lc_raw_intent == 'popup') return 'popup';
if (lc_raw_intent == 'api') return 'api';
if (lc_raw_intent == 'dom') return 'dom';
if (lc_raw_intent == 'js') return 'js';
if (lc_raw_intent == 'timeout') return 'timeout';

// final check for recognized code before returning error
if (is_code(raw_intent)) return 'code'; else return 'error';}

function is_code(raw_intent) {
// due to asynchronous waiting for element, if/for/while can work for parsing single step
// other scenarios can be assumed to behave as unparsed javascript in casperjs context
// to let if/for/while handle multiple steps/code use the { and } steps to define block
if ((raw_intent.substr(0,4) == 'var ') || (raw_intent.substr(0,3) == 'do ')) return true;
if ((raw_intent.substr(0,1) == '{') || (raw_intent.substr(0,1) == '}')) return true;
if ((raw_intent.charAt(raw_intent.length-1) == '{') || (raw_intent.charAt(raw_intent.length-1) == '}')) return true;
if ((raw_intent.substr(0,3) == 'if ') || (raw_intent.substr(0,4) == 'else')) return true;
if ((raw_intent.substr(0,4) == 'for ') || (raw_intent.substr(0,6) == 'while ')) return true;
if ((raw_intent.substr(0,7) == 'switch ') || (raw_intent.substr(0,5) == 'case ')) return true;
if ((raw_intent.substr(0,6) == 'break;') || (raw_intent.substr(0,9) == 'function ')) return true;
if ((raw_intent.substr(0,7) == 'casper.') || (raw_intent.substr(0,5) == 'this.')) return true;
if (raw_intent.substr(0,7) == 'chrome.') return true; // chrome object for chrome integration
if (raw_intent.substr(0,5) == 'test.') return true;
if ((raw_intent.substr(0,2) == '//') || (raw_intent.charAt(raw_intent.length-1) == ';')) return true;
// assume = is assignment statement, kinda acceptable as this is checked at the very end
if (raw_intent.indexOf('=') > -1) return true; return false;}

function abs_file(filename) { // helper function to return absolute filename
if (filename == '') return ''; // unlike tagui_parse.php not deriving path from script variable
if (filename.substr(0,1) == '/') return filename; // return mac/linux absolute filename directly
if (filename.substr(1,1) == ':') return filename.replace(/\\/g,'/'); // return windows absolute filename directly
var tmp_flow_path = flow_path; // otherwise use flow_path defined in generated script to build absolute filename
// above str_replace is because casperjs/phantomjs do not seem to support \ for windows paths, replace with / to work
if (tmp_flow_path.indexOf('/') > -1) return tmp_flow_path + '/' + filename; else return tmp_flow_path + '\\' + filename;}

function add_concat(source_string) { // parse string and add missing + concatenator
if ((source_string.indexOf("'") > -1) && (source_string.indexOf('"') > -1))
return "'ERROR - inconsistent quotes in text'";
else if (source_string.indexOf("'") > -1) var quote_type = "'"; // derive quote type used
else if (source_string.indexOf('"') > -1) var quote_type = '"'; else var quote_type = "none";
var within_quote = false; source_string = source_string.trim(); // trim for future proof
for (srcpos = 0; srcpos < source_string.length; srcpos++) {
if (source_string.charAt(srcpos) == quote_type) within_quote = !(within_quote);
if ((within_quote == false) && (source_string.charAt(srcpos)==" "))
source_string = source_string.substring(0,srcpos) + "+" + source_string.substring(srcpos+1);}
source_string = source_string.replace(/\+\+\+\+\+/g,'+'); source_string = source_string.replace(/\+\+\+\+/g,'+');
source_string = source_string.replace(/\+\+\+/g,'+'); source_string = source_string.replace(/\+\+/g,'+');
return source_string;} // replacing multiple variations of + to handle user typos of double spaces etc

function is_sikuli(input_params) { // helper function to check if input is meant for sikuli visual automation
if (input_params.length > 4 && input_params.substr(-4).toLowerCase() == '.png') return true; // support png and bmp
else if (input_params.length > 4 && input_params.substr(-4).toLowerCase() == '.bmp') return true; else return false;}

function call_sikuli(input_intent,input_params) { // helper function to use sikuli visual automation
var fs = require('fs'); // use phantomjs fs file system module to access files and directories
fs.write('tagui.sikuli/tagui_sikuli.in', '', 'w'); fs.write('tagui.sikuli/tagui_sikuli.out', '', 'w');
if (!fs.exists('tagui.sikuli/tagui_sikuli.in')) return "this.echo('ERROR - cannot initialise tagui_sikuli.in')";
if (!fs.exists('tagui.sikuli/tagui_sikuli.out')) return "this.echo('ERROR - cannot initialise tagui_sikuli.out')";
return "var fs = require('fs'); if (!sikuli_step('"+input_intent+"')) if (!fs.exists('"+input_params+"')) " +
"this.echo('ERROR - cannot find image file "+input_params+"'); " +
"else this.echo('ERROR - cannot find "+input_params+" on screen');";}

function check_chrome_context(source_code) { // function to convert javascript code to chrome context
// specifically for live mode, as statements in flow file are already converted by tagui_parse.php
if (chrome_id == 0) return source_code; // if chrome or headless option is used, chrome_id will be > 0
source_code = source_code.replace(/casper\.exists/g,'chrome.exists').replace(/this\.exists/g,'chrome.exists');
source_code = source_code.replace(/casper\.click/g,'chrome.click').replace(/this\.click/g,'chrome.click');
source_code = source_code.replace(/casper\.mouse/g,'chrome.mouse').replace(/this\.mouse/g,'chrome.mouse');
source_code = source_code.replace(/casper\.sendKeys/g,'chrome.sendKeys').replace(/this\.sendKeys/g,'chrome.sendKeys');
source_code = source_code.replace(/casper\.selectOptionByValue/g,'chrome.selectOptionByValue').replace(/this\.selectOptionByValue/g,'chrome.selectOptionByValue');
source_code = source_code.replace(/casper\.fetchText/g,'chrome.fetchText').replace(/this\.fetchText/g,'chrome.fetchText');
source_code = source_code.replace(/casper\.capture/g,'chrome.capture').replace(/this\.capture/g,'chrome.capture');
source_code = source_code.replace(/casper\.captureSelector/g,'chrome.captureSelector').replace(/this\.captureSelector/g,'chrome.captureSelector');
source_code = source_code.replace(/chrome\.page\.uploadFile/g,'chrome.upload').replace(/casper\.page\.uploadFile/g,'chrome.upload').replace(/this\.page\.uploadFile/g,'chrome.upload');
source_code = source_code.replace(/casper\.download/g,'chrome.download').replace(/this\.download/g,'chrome.download');
source_code = source_code.replace(/casper\.evaluate/g,'chrome.evaluate').replace(/this\.evaluate/g,'chrome.evaluate');
source_code = source_code.replace(/casper\.getHTML/g,'chrome.getHTML').replace(/this\.getHTML/g,'chrome.getHTML');
source_code = source_code.replace(/casper\.getTitle/g,'chrome.getTitle').replace(/this\.getTitle/g,'chrome.getTitle');
source_code = source_code.replace(/casper\.getCurrentUrl/g,'chrome.getCurrentUrl').replace(/this\.getCurrentUrl/g,'chrome.getCurrentUrl');
source_code = source_code.replace(/casper\.debugHTML/g,'chrome.debugHTML').replace(/this\.debugHTML/g,'chrome.debugHTML');
source_code = source_code.replace(/casper\.reload/g,'chrome.reload').replace(/this\.reload/g,'chrome.reload');
source_code = source_code.replace(/casper\.back/g,'chrome.back').replace(/this\.back/g,'chrome.back');
source_code = source_code.replace(/casper\.forward/g,'chrome.forward').replace(/this\.forward/g,'chrome.forward');
return source_code;};

// for calling rest api url synchronously
function call_api(rest_url) { // advance users can define api_config for advance calls
// the api_config variable defaults to {method:'GET', header:[], body:{}}
var xhttp = new XMLHttpRequest(); xhttp.open(api_config.method, rest_url, false);
for (var item=0;item<api_config.header.length;item++) { // process headers
if (api_config.header[item] == '') continue; // skip if header is not defined
var header_value_pair = api_config.header[item].split(':'); // format is 'Header_name: header_value'
xhttp.setRequestHeader(header_value_pair[0].trim(),header_value_pair[1].trim());}
xhttp.send(JSON.stringify(api_config.body)); return xhttp.responseText;}

// custom function to handle dropdown option
casper.selectOptionByValue = function(selector, valueToMatch) { // solution posted in casperjs issue #1390
this.evaluate(function(selector, valueToMatch) {var found = false; // modified to allow xpath / css locators
if ((selector.indexOf('/') == 0) || (selector.indexOf('(') == 0)) var select = __utils__.getElementByXPath(selector);
else var select = document.querySelector(selector); // auto-select xpath or query css method to get element
Array.prototype.forEach.call(select.children, function(opt, i) { // loop through list to select option
if (!found && opt.value.indexOf(valueToMatch) !== -1) {select.selectedIndex = i; found = true;}});
var evt = document.createEvent("UIEvents"); // dispatch change event in case there is validation
evt.initUIEvent("change", true, true); select.dispatchEvent(evt);}, selector, valueToMatch);};

// flow path for save_text and snap_image
