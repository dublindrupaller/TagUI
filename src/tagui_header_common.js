// xpath for object identification
var x = require('casper').selectXPath;

// assign parameters to p1-p9 variables
var p1 = casper.cli.raw.get(0); var p2 = casper.cli.raw.get(1); var p3 = casper.cli.raw.get(2);
var p4 = casper.cli.raw.get(3); var p5 = casper.cli.raw.get(4); var p6 = casper.cli.raw.get(5);
var p7 = casper.cli.raw.get(6); var p8 = casper.cli.raw.get(7); var p9 = casper.cli.raw.get(8);

// save start time to measure execution time
var automation_start_time = Date.now(); casper.echo('\nSTART - automation started - ' + Date().toLocaleString());

// initialise default global variables
var quiet_mode = false; var save_text_count = 0; var snap_image_count = 0;

// counters for tracking messages in sikuli and chrome integrations
var sikuli_count = 0; var chrome_id = 0;

// chrome context for frame handling and targetid for popup handling
var chrome_context = 'document'; var chrome_targetid = '';

// JSON variable to pass variables into browser DOM
var dom_json = {}; var dom_result = '';

// variable for advance usage of api step
var api_config = {method:'GET', header:[], body:{}};

// techo function for handling quiet option
function techo(echo_string) {if (!quiet_mode) casper.echo(echo_string); return;}

// for muting echo in test automation scripts
function dummy_echo(muted_string) {return;}

// for saving text information to file
function save_text(file_name,info_text) {var ds; if (flow_path.indexOf('/') !== -1) ds = '/'; else ds = '\\';
if (!file_name) {save_text_count++; file_name = flow_path + ds + 'text' + save_text_count.toString() + '.txt';}
var fs = require('fs'); fs.write(file_name, info_text, 'w');}

// for appending text information to file
function append_text(file_name,info_text) {var ds; if (flow_path.indexOf('/') !== -1) ds = '/'; else ds = '\\';
if (!file_name) {if (save_text_count==0) save_text_count++; // increment if 0, else use same count to append
file_name = flow_path + ds + 'text' + save_text_count.toString() + '.txt';}
var fs = require('fs'); fs.write(file_name, info_text + '\r\n', 'a');}

// for saving snapshots of website to file
function snap_image() {var ds; if (flow_path.indexOf('/') !== -1) ds = '/'; else ds = '\\';
snap_image_count++; return (flow_path + ds + 'snap' + snap_image_count.toString() + '.png');}

// for saving table from website to file
function save_table(file_name,selector) {var ds; if (flow_path.indexOf('/') !== -1) ds = '/'; else ds = '\\';
if (!file_name) {save_text_count++; file_name = flow_path + ds + 'table' + save_text_count.toString() + '.csv';}
var row_data = ""; var table_cell = ""; var fs = require('fs'); fs.write(file_name, '', 'w'); // always reset file
if (!casper.exists(selector) || (selector.toString().indexOf('xpath selector: ')==-1)) return false; // exit if invalid
if (selector.toString().length == 16) selector = ''; else selector = selector.toString().substring(16); // get xpath
for (table_row=1; table_row<=1024; table_row++) {row_data = ""; for (table_col=1; table_col<=1024; table_col++) {
table_cell = '(((' + selector + '//tr)[' + table_row + ']//th)' + '|'; // build cell xpath selector to include
table_cell += '((' + selector + '//tr)[' + table_row + ']//td))[' + table_col + ']'; // both td and td elements
if (casper.exists(x(table_cell))) row_data = row_data + '","' + casper.fetchText(x(table_cell)).trim();
else break;} // if searching for table cells (th and td) is not successful,  means end of row is reached
if (row_data.substr(0,2) == '",') {row_data = row_data.substr(2); row_data += '"'; append_text(file_name,row_data);}
else return true;}} // if '",' is not found, means end of table is reached as there is no cell found in row

// for checking if selector is xpath selector
function is_xpath_selector(selector) {if (selector.length == 0) return false;
if ((selector.indexOf('/') == 0) || (selector.indexOf('(') == 0)) return true; return false;}

// for finding best match for given locator
function tx(locator) {if (is_xpath_selector(locator)) return x(locator);
if (casper.exists(locator)) return locator; // check for css locator
// first check for exact match then check for containing string
if (casper.exists(x('//*[@id="'+locator+'"]'))) return x('//*[@id="'+locator+'"]');
if (casper.exists(x('//*[contains(@id,"'+locator+'")]'))) return x('//*[contains(@id,"'+locator+'")]');
if (casper.exists(x('//*[@name="'+locator+'"]'))) return x('//*[@name="'+locator+'"]');
if (casper.exists(x('//*[contains(@name,"'+locator+'")]'))) return x('//*[contains(@name,"'+locator+'")]');
if (casper.exists(x('//*[@class="'+locator+'"]'))) return x('//*[@class="'+locator+'"]');
if (casper.exists(x('//*[contains(@class,"'+locator+'")]'))) return x('//*[contains(@class,"'+locator+'")]');
if (casper.exists(x('//*[@title="'+locator+'"]'))) return x('//*[@title="'+locator+'"]');
if (casper.exists(x('//*[contains(@title,"'+locator+'")]'))) return x('//*[contains(@title,"'+locator+'")]');
if (casper.exists(x('//*[@aria-label="'+locator+'"]'))) return x('//*[@aria-label="'+locator+'"]');
if (casper.exists(x('//*[contains(@aria-label,"'+locator+'")]'))) return x('//*[contains(@aria-label,"'+locator+'")]');
if (casper.exists(x('//*[text()="'+locator+'"]'))) return x('//*[text()="'+locator+'"]');
if (casper.exists(x('//*[contains(text(),"'+locator+'")]'))) return x('//*[contains(text(),"'+locator+'")]');
if (casper.exists(x('//*[@href="'+locator+'"]'))) return x('//*[@href="'+locator+'"]');
if (casper.exists(x('//*[contains(@href,"'+locator+'")]'))) return x('//*[contains(@href,"'+locator+'")]');
return x('/html');}

// for checking if given locator is found
function check_tx(locator) {if (is_xpath_selector(locator))
{if (casper.exists(x(locator))) return true; else return false;}
if (casper.exists(locator)) return true; // check for css locator
// first check for exact match then check for containing string
if (casper.exists(x('//*[@id="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(@id,"'+locator+'")]'))) return true;
if (casper.exists(x('//*[@name="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(@name,"'+locator+'")]'))) return true;
if (casper.exists(x('//*[@class="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(@class,"'+locator+'")]'))) return true;
if (casper.exists(x('//*[@title="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(@title,"'+locator+'")]'))) return true;
if (casper.exists(x('//*[@aria-label="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(@aria-label,"'+locator+'")]'))) return true;
if (casper.exists(x('//*[text()="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(text(),"'+locator+'")]'))) return true;
if (casper.exists(x('//*[@href="'+locator+'"]'))) return true;
if (casper.exists(x('//*[contains(@href,"'+locator+'")]'))) return true;
return false;}

// friendlier name to use check_tx() in if condition in flow
function present(element_locator) {if (!element_locator) return false; else return check_tx(element_locator);}

function sleep(ms) { // helper to add delay during loops
var time_now = new Date().getTime(); var time_end = time_now + ms;
while(time_now < time_end) {time_now = new Date().getTime();}}
