// for initialising integration with chrome web browser
function chrome_handshake() { // techo('[connecting to chrome websocket]');
var fs = require('fs'); fs.write('tagui_chrome.in','','w'); var chrome_handshake = '';
if (!fs.exists('tagui_chrome.out')) fs.write('tagui_chrome.out','','w');
do {sleep(100); chrome_handshake = fs.read('tagui_chrome.out').trim();}
while (chrome_handshake !== '[0] START'); // techo('[connected to chrome websocket]');
}

// send websocket message to chrome browser using chrome devtools protocol
// php helper process tagui_chrome.php running to handle this concurrently
function chrome_step(method,params) {chrome_id++;
if (chrome_id == 1) chrome_handshake(); // handshake on first call
var chrome_intent = JSON.stringify({'id': chrome_id, 'method': method, 'params': params});
if (chrome_targetid !== '') chrome_intent = JSON.stringify({'id': chrome_id, 'method': 'Target.sendMessageToTarget', 'params': {'targetId': chrome_targetid, 'message': chrome_intent}}); // send as message to target if context is popup
var fs = require('fs'); fs.write('tagui_chrome.in','['+chrome_id.toString()+'] '+chrome_intent,'w');
var chrome_result = ''; do {sleep(100); chrome_result = fs.read('tagui_chrome.out').trim();}
while (chrome_result.indexOf('['+chrome_id.toString()+'] ') == -1);
if (chrome_targetid == '') return chrome_result.substring(chrome_result.indexOf('] ')+2); // below for handling popup
else {try {var raw_json_string = JSON.stringify(JSON.parse(chrome_result.substring(chrome_result.indexOf('] ')+2)).params.message); return raw_json_string.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\n/g,"\\n");} catch(e) {return '';}}}

// chrome object for handling integration with chrome or headless chrome
var chrome = new Object(); chrome.mouse = new Object();

// chrome methods as casper methods replacement for chrome integration
chrome.exists = function(selector) { // different handling for xpath and css to support both
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16);
var ws_message = chrome_step('Runtime.evaluate',{expression: 'document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotLength'});}
else var ws_message = chrome_step('Runtime.evaluate',{expression: chrome_context+'.querySelectorAll(\''+selector+'\').length'});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value > 0) return true; else return false;}
catch(e) {return false;}};

chrome.click = function(selector) { // click by sending click event instead of mouse down/up/click, then focus on element
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16);
chrome_step('Runtime.evaluate',{expression: 'document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).click()'}); chrome_step('Runtime.evaluate',{expression: 'document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).focus()'});}
else {chrome_step('Runtime.evaluate',{expression: chrome_context+'.querySelector(\''+selector+'\').click()'});
chrome_step('Runtime.evaluate',{expression: chrome_context+'.querySelector(\''+selector+'\').focus()'});}};

chrome.mouse.action = function(type,x,y,button,clickCount) { // helper function to send various mouse events
chrome_step('Input.dispatchMouseEvent',{type: type, x: x, y: y, button: button, clickCount: clickCount});};

chrome.mouse.getXY = function(selector) { // helper function to get xy center coordinates of selector
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16);
var ws_message = chrome_step('Runtime.evaluate',{expression: 'var result_bounds = document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).getBoundingClientRect(); var result_xy = {x: Math.round(result_bounds.left + result_bounds.width / 2), y: Math.round(result_bounds.top + result_bounds.height / 2)}; result_xy', returnByValue: true});}
else var ws_message = chrome_step('Runtime.evaluate',{expression: 'var result_bounds = '+chrome_context+'.querySelector(\''+selector+'\').getBoundingClientRect(); var result_xy = {x: Math.round(result_bounds.left + result_bounds.width / 2), y: Math.round(result_bounds.top + result_bounds.height / 2)}; result_xy', returnByValue: true});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value.x > 0 && ws_json.result.result.value.y > 0)
return ws_json.result.result.value; else return {x: 0, y: 0};} catch(e) {return {x: 0, y: 0};}};

chrome.getRect = function(selector) { // helper function to get rectangle boundary coordinates of selector
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16);
var ws_message = chrome_step('Runtime.evaluate',{expression: 'var result_bounds = document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).getBoundingClientRect(); var result_rect = {top: Math.round(result_bounds.top), left: Math.round(result_bounds.left), width: Math.round(result_bounds.width), height: Math.round(result_bounds.height)}; result_rect', returnByValue: true});}
else var ws_message = chrome_step('Runtime.evaluate',{expression: 'var result_bounds = '+chrome_context+'.querySelector(\''+selector+'\').getBoundingClientRect(); var result_rect = {top: Math.round(result_bounds.top), left: Math.round(result_bounds.left), width: Math.round(result_bounds.width), height: Math.round(result_bounds.height)}; result_rect', returnByValue: true}); try {var ws_json = JSON.parse(ws_message); // check if width and height are valid before returning coordinates
if (ws_json.result.result.value.width > 0 && ws_json.result.result.value.height > 0) return ws_json.result.result.value;
else return {left: 0, top: 0, width: 0, height: 0};} catch(e) {return {left: 0, top: 0, width: 0, height: 0};}};

chrome.mouse.move = function(selector,y) { // move mouse pointer to center of specified selector or point 
if (!y) var xy = chrome.mouse.getXY(selector); else var xy = {x: selector, y: y}; // get coordinates accordingly
chrome.mouse.action('mouseMoved',xy.x,xy.y,'none',0);};

chrome.mouse.click = function(selector,y) { // press and release on center of specfied selector or point
if (!y) var xy = chrome.mouse.getXY(selector); else var xy = {x: selector, y: y}; // get coordinates accordingly
chrome.mouse.action('mousePressed',xy.x,xy.y,'left',1); chrome.mouse.action('mouseReleased',xy.x,xy.y,'left',1);};

chrome.mouse.doubleclick = function(selector,y) { // double press and release on center of selector or point
if (!y) var xy = chrome.mouse.getXY(selector); else var xy = {x: selector, y: y}; // get coordinates accordingly
chrome.mouse.action('mousePressed',xy.x,xy.y,'left',1); chrome.mouse.action('mouseReleased',xy.x,xy.y,'left',1);
chrome.mouse.action('mousePressed',xy.x,xy.y,'left',1); chrome.mouse.action('mouseReleased',xy.x,xy.y,'left',1);};

chrome.mouse.rightclick = function(selector,y) { // right click press and release on center of selector or point
if (!y) var xy = chrome.mouse.getXY(selector); else var xy = {x: selector, y: y}; // get coordinates accordingly
chrome.mouse.action('mousePressed',xy.x,xy.y,'right',1); chrome.mouse.action('mouseReleased',xy.x,xy.y,'right',1);};

chrome.mouse.down = function(selector,y) { // left press on center of specified selector or point
if (!y) var xy = chrome.mouse.getXY(selector); else var xy = {x: selector, y: y}; // get coordinates accordingly
chrome.mouse.action('mousePressed',xy.x,xy.y,'left',1);};

chrome.mouse.up = function(selector,y) { // left release on center of specified selector or point
if (!y) var xy = chrome.mouse.getXY(selector); else var xy = {x: selector, y: y}; // get coordinates accordingly
chrome.mouse.action('mouseReleased',xy.x,xy.y,'left',1);};

chrome.sendKeys = function(selector,value,options) { // send key strokes to selector, options not implemented
if (value == casper.page.event.key.Enter) value = '\r';
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16);
chrome_step('Runtime.evaluate',{expression: 'document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).focus()'});}
else chrome_step('Runtime.evaluate',{expression: chrome_context+'.querySelector(\''+selector+'\').focus()'});
if (options && options.reset == true) // handling for clearing field by checking options.reset
{if ((selector.indexOf('/') == 0) || (selector.indexOf('(') == 0)) // check for xpath selector and handle accordingly
{chrome_step('Runtime.evaluate',{expression: 'var sendKeys_field = document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0); sendKeys_field.value = \'\'; var evt = document.createEvent(\'UIEvents\'); evt.initUIEvent(\'change\', true, true); sendKeys_field.dispatchEvent(evt);'});}
else chrome_step('Runtime.evaluate',{expression: 'var sendKeys_field = '+chrome_context+'.querySelector(\''+selector+'\'); sendKeys_field.value = \'\'; var evt = document.createEvent(\'UIEvents\'); evt.initUIEvent(\'change\', true, true); sendKeys_field.dispatchEvent(evt);'});}
for (var character = 0, length = value.length; character < length; character++) {
chrome_step('Input.dispatchKeyEvent',{type: 'char', text: value[character]});}};

chrome.selectOptionByValue = function(selector,valueToMatch) { // select dropdown option (base on casperjs issue #1390)
chrome.evaluate('function() {var selector = \''+selector+'\'; var valueToMatch = \''+valueToMatch+'\'; var found = false; if ((selector.indexOf(\'/\') == 0) || (selector.indexOf(\'(\') == 0)) var select = document.evaluate(selector,'+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0); else var select = '+chrome_context+'.querySelector(selector); Array.prototype.forEach.call(select.children, function(opt, i) {if (!found && opt.value.indexOf(valueToMatch) !== -1) {select.selectedIndex = i; found = true;}}); var evt = document.createEvent("UIEvents"); evt.initUIEvent("change", true, true); select.dispatchEvent(evt);}');};

chrome.fetchText = function(selector) { // grab text from selector following casperjs logic, but grab only first match
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16);
var ws_message = chrome_step('Runtime.evaluate',{expression: 'document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).textContent || document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).innerText || document.evaluate(\''+selector+'\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).value || \'\''});}
else var ws_message = chrome_step('Runtime.evaluate',{expression: chrome_context+'.querySelector(\''+selector+'\').textContent || '+chrome_context+'.querySelector(\''+selector+'\').innerText || '+chrome_context+'.querySelector(\''+selector+'\').value || \'\''});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.decode = function(str) { // funtion to convert base64 data to binary string
// used in https://github.com/casperjs/casperjs/blob/master/modules/clientutils.js
if (!str) return ''; // return empty string if somehow null value is passed in
var BASE64_DECODE_CHARS = [
-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,
-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,62,-1,-1,-1,63,52,53,54,55,56,57,58,59,60,61,-1,-1,-1,-1,-1,-1,
-1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,-1,-1,-1,-1,-1,
-1,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,-1,-1,-1,-1,-1];
var c1, c2, c3, c4, i = 0, len = str.length, out = ""; while (i < len) {
do {c1 = BASE64_DECODE_CHARS[str.charCodeAt(i++) & 0xff];} while (i < len && c1 === -1); if (c1 === -1) {break;}
do {c2 = BASE64_DECODE_CHARS[str.charCodeAt(i++) & 0xff];} while (i < len && c2 === -1); if (c2 === -1) {break;}
out += String.fromCharCode(c1 << 2 | (c2 & 0x30) >> 4);
do {c3 = str.charCodeAt(i++) & 0xff; if (c3 === 61) {return out;} c3 = BASE64_DECODE_CHARS[c3];}
while (i < len && c3 === -1); if (c3 === -1) {break;} out += String.fromCharCode((c2 & 0XF) << 4 | (c3 & 0x3C) >> 2);
do {c4 = str.charCodeAt(i++) & 0xff; if (c4 === 61) {return out;} c4 = BASE64_DECODE_CHARS[c4];}
while (i < len && c4 === -1); if (c4 === -1) {break;} out += String.fromCharCode((c3 & 0x03) << 6 | c4);} return out;};

chrome.capture = function(filename) { // capture screenshot of webpage to file in png/jpg/jpeg format
// having pdf extension saves to a pdf file instead. only works in headless mode, visible mode errors out
var format = 'png'; var quality = 80; var fromSurface = true; var screenshot_data = ''; // options not implemented
if ((filename.substr(-3).toLowerCase() == 'jpg') || (filename.substr(-4).toLowerCase() == 'jpeg')) format = 'jpeg';
if (filename.substr(-3).toLowerCase() == 'pdf') var ws_message = chrome_step('Page.printToPDF',{printBackground: true});
else var ws_message = chrome_step('Page.captureScreenshot',{format: format, quality: quality, fromSurface: fromSurface});
try {var ws_json = JSON.parse(ws_message); screenshot_data = ws_json.result.data;} catch(e) {screenshot_data = '';}
var fs = require('fs'); fs.write(filename,chrome.decode(screenshot_data),'wb');};

chrome.captureSelector = function(filename,selector) { // capture screenshot of selector to png/jpg/jpeg format
// first capture entire screen, then use casperjs / phantomjs browser to crop image base on selector dimensions
chrome.capture(filename); var selector_rect = chrome.getRect(selector); // so that there is no extra dependency
if (selector_rect.width > 0 && selector_rect.height > 0) // from using other libraries or creating html canvas 
casper.thenOpen(filename, function() {casper . capture(filename, // spaces around . intentional to avoid replacing 
{top: selector_rect.top, left: selector_rect.left, width: selector_rect.width, height: selector_rect.height});
casper.thenOpen('about:blank');});}; // reset phantomjs browser state

chrome.upload = function(selector,filename) { // upload function to upload file to provided selector
if ((selector.toString().length >= 16) && (selector.toString().substr(0,16) == 'xpath selector: '))
{casper.echo('ERROR - upload step is only implemented for CSS selector and not XPath selector');
casper.echo('ERROR - for consistency with PhantomJS as it only supports upload with CSS selector');}
else try {var ws_message = ""; var ws_json = {};
ws_message = chrome_step('DOM.getDocument',{});
ws_json = JSON.parse(ws_message);
ws_message = chrome_step('DOM.querySelector',{nodeId: ws_json.result.root.nodeId, selector: selector});
ws_json = JSON.parse(ws_message);
ws_message = chrome_step('DOM.setFileInputFiles',{files: [filename], nodeId: ws_json.result.nodeId});
} catch(e) {casper.echo('ERROR - unable to upload ' + selector + ' as ' + filename);}};

chrome.download = function(url,filename) { // download function for downloading url resource to file
// casper download cannot be used for url which requires login as casperjs context is not in chrome
// the chromium issue seems to be moving, otherwise another way may be to inject casper clientutils.js
casper.echo('ERROR - for visible Chrome, download file directly through normal webpage interaction');
casper.echo('ERROR - for headless Chrome, it prevents file download for now - Chromium issue 696481');};

chrome.evaluate = function(fn_statement,eval_json) { // evaluate expression in browser dom context
// chrome runtime.evaluate is different from casperjs evaluate, do some processing to reduce gap
var statement = fn_statement.toString(); if (!eval_json)
{statement = statement.slice(statement.indexOf('{')+1,statement.lastIndexOf('}'));
statement = statement.replace(/return /g,'');} // defining function() with return keyword is invalid for chrome
else statement = '(' + statement + ')' + '(' + JSON.stringify(eval_json) + ')'; // unless variable is passed into fx
var ws_message = chrome_step('Runtime.evaluate',{expression: statement}); // statements can be separated by ;
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return null;} catch(e) {return null;}};

chrome.withFrame = function(frameInfo,then) { // replace casperjs frame for switching frame context
var new_context = ''; if (chrome_context == 'document') new_context = 'mainframe_context';
else if (chrome_context == 'mainframe_context') new_context = 'subframe_context';
casper.then(function _step() {chrome_step('Runtime.evaluate',{expression: new_context+' = document.evaluate(\'(//frame|//iframe)[@name="'+frameInfo+'" or @id="'+frameInfo+'"]\','+chrome_context+',null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null).snapshotItem(0).contentDocument'}); chrome_context = new_context;}); // set mainframe_context/subframe_context in dom
casper.then(then); casper.then(function _step() {if (chrome_context == 'subframe_context') {chrome_step('Runtime.evaluate',{expression: 'subframe_context = null'}); chrome_context = 'mainframe_context';} else if (chrome_context == 'mainframe_context') {chrome_step('Runtime.evaluate',{expression: 'mainframe_context = null'}); chrome_context = 'document';}});};

chrome.waitForPopup = function(popupInfo,then,onTimeout) { // replace casperjs waitforpopup for checking popup window
casper.waitFor(function check() { // use similar logic as chrome withpopup to scan through list of browser targets
var found_popup = false; var chrome_targets = []; var ws_message = chrome_step('Target.getTargets',{});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.targetInfos) chrome_targets = ws_json.result.targetInfos;
else chrome_targets = [];} catch(e) {chrome_targets = [];} // following line scan through targets to find match
chrome_targets.forEach(function(target) {if (target.url.search(popupInfo) !== -1) found_popup = true;});
return found_popup;},then,onTimeout);};

chrome.withPopup = function(popupInfo,then) { // replace casperjs withpopup for handling popup window
casper.then(function _step() { // get list of targets, find a match, attach to the target and set chrome_targetid
var found_targetid = ''; var chrome_targets = []; var ws_message = chrome_step('Target.getTargets',{});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.targetInfos) chrome_targets = ws_json.result.targetInfos;
else chrome_targets = [];} catch(e) {chrome_targets = [];} // following line scan through targets to find match
chrome_targets.forEach(function(target) {if (target.url.search(popupInfo) !== -1) found_targetid = target.targetId;});
if (found_targetid !== '') {var ws_message = chrome_step('Target.attachToTarget',{targetId: found_targetid}); try {var ws_json = JSON.parse(ws_message); if (ws_json.result.success !== true) found_targetid = '';} catch(e) {found_targetid = ''};}
chrome_targetid = found_targetid;}); // set chrome_targetid only after attaching to found target successfully
casper.then(then); casper.then(function _step() {if (chrome_targetid !== '') // detach from target after running then
{var found_targetid = chrome_targetid; chrome_targetid = ''; chrome_step('Target.detachFromTarget',{targetId: found_targetid});}});};

chrome.getHTML = function() { // get raw html of current webpage
var ws_message = chrome_step('Runtime.evaluate',{expression: 'document.documentElement.outerHTML'});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.getTitle = function() { // get title of current webpage
var ws_message = chrome_step('Runtime.evaluate',{expression: 'document.title'});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.getCurrentUrl = function() { // get url of current webpage
var ws_message = chrome_step('Runtime.evaluate',{expression: 'document.location.href'});
try {var ws_json = JSON.parse(ws_message); // chrome returns below value on empty dead url
if (ws_json.result.result.value == 'data:text/html,chromewebdata') ws_json.result.result.value = 'about:blank';
if (ws_json.result.result.value) return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.debugHTML = function() {casper.echo(chrome.getHTML());}; // print raw html of current webpage

chrome.reload = function() { // reload the current webpage, then not implemented
var ws_message = chrome_step('Runtime.evaluate',{expression: 'document.location.reload()'});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.back = function() { // move back a step in browser history
var ws_message = chrome_step('Runtime.evaluate',{expression: 'window.history.back()'});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.forward = function() { // move forward a step in browser history
var ws_message = chrome_step('Runtime.evaluate',{expression: 'window.history.forward()'});
try {var ws_json = JSON.parse(ws_message); if (ws_json.result.result.value)
return ws_json.result.result.value; else return '';} catch(e) {return '';}};

chrome.echo = function(value) {casper.echo(value);}; // use casper echo to print output

chrome.on = function(value,statement) {casper.on(value,statement);}; // use casper event system

// end of super large if block to load chrome related functions if chrome or headless option is used
