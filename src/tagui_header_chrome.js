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
