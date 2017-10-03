// for initialising integration with sikuli visual automation
function sikuli_handshake() { // techo('[connecting to sikuli process]');
var ds; if (flow_path.indexOf('/') !== -1) ds = '/'; else ds = '\\';
var fs = require('fs'); fs.write('tagui.sikuli'+ds+'tagui_sikuli.in','','w'); var sikuli_handshake = '';
if (!fs.exists('tagui.sikuli'+ds+'tagui_sikuli.out')) fs.write('tagui.sikuli'+ds+'tagui_sikuli.out','','w');
do {sleep(500); sikuli_handshake = fs.read('tagui.sikuli'+ds+'tagui_sikuli.out').trim();}
while (sikuli_handshake !== '[0] START'); // techo('[connected to sikuli process]');
}

// for using sikuli visual automation instead of casperjs
function sikuli_step(sikuli_intent) {sikuli_count++;
if (sikuli_count == 1) sikuli_handshake(); // handshake on first call
var ds; if (flow_path.indexOf('/') !== -1) ds = '/'; else ds = '\\';
var fs = require('fs'); fs.write('tagui.sikuli'+ds+'tagui_sikuli.in','['+sikuli_count.toString()+'] '+sikuli_intent,'w');
var sikuli_result = ''; do {sleep(500); sikuli_result = fs.read('tagui.sikuli'+ds+'tagui_sikuli.out').trim();}
while (sikuli_result.indexOf('['+sikuli_count.toString()+'] ') == -1);
if (sikuli_result.indexOf('SUCCESS') !== -1) return true; else return false;}

if (chrome_id > 0) { // super large if block to load chrome related functions if chrome or headless option is used
chrome_id = 0; // reset chrome_id from 1 back to 0 to prepare for initial call of chrome_step
