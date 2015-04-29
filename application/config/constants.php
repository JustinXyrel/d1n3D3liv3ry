<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

#DEFINE TERMINAL DETAILS
define('TERMINAL_ID',			'1');

#DEFINE BRANCH DETAILS
define('BRANCH_CODE',			'ELRGB');

define('SALES_TRANS',			'10');
define('SALES_VOID_TRANS',		'11');
define('RECEIVE_TRANS',			'20');
define('ADJUSTMENT_TRANS',		'30');
define('CALL_CENTER_TRANS',		'40');
define('DELIVERY_CODE',			'50');
define('DELIVERY_RECEIPT', 		'60');

define('DELIVERY_CHARGE_ID',	'2');

#DEFINE BASE_TAX
define('BASE_TAX',0.12); // Please use base tax form (eg. 0.12) not num form (12)

#DEFINE READ TYPE
define('X_READ',1);
define('Z_READ',2);

#DEFINE REPORTS HEADER SUBJECT
define('REPORTS_HEADER_SUBJECT', 	'Call Center - www.call-center.com');
define('SENIOR_CITIZEN_ID', 		'1');
define('SENIOR_CITIZEN_CODE', 		'SNDISC');


/* End of file constants.php */
/* Location: ./application/config/constants.php */