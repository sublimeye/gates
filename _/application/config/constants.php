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


/*
|--------------------------------------------------------------------------
| DataBase tables constants
|--------------------------------------------------------------------------
*/

define('USERS_DB_TABLE','users');
define('NEWS_DB_TABLE','news');
define('PAGES_DB_TABLE','pages');
define('LANGUAGES_DB_TABLE','languages');
define('TOWN_DB_TABLE','town');
define('BUILDING_DB_TABLE','building');
define('BUILDING_IMAGES_DB_TABLE','building_images');
define('BUILDING_PLANS_DB_TABLE','building_plan');
define('BUILDING_PLACES_DB_TABLE','building_places');
define('BUILDING_PLACE_IMAGES_DB_TABLE','building_places_images');
define('LANGUAGE_DB_TABLE','languages');

/*
|--------------------------------------------------------------------------
| ITEMS STATUS
|--------------------------------------------------------------------------
*/

define('STATUS_ENABLED',1);
define('STATUS_DISABLED',0);

/*
|--------------------------------------------------------------------------
| Project state constants
|--------------------------------------------------------------------------
*/

define('PROJECT_IN_PRODUCTION',0);
define('PROJECT_IN_DEBUGGING',1);
define('PROJECT_IN_DEVELOPMENT',2);

/*
|--------------------------------------------------------------------------
| Constants mode logging to a file
|--------------------------------------------------------------------------
*/

define('LOG_FILE_REWRITE',0);
define('LOG_FILE_APPEND',1);

/*
|--------------------------------------------------------------------------
| Constants type message
|--------------------------------------------------------------------------
*/

define('MESSAGE_ERROR',0);
define('MESSAGE_WARNING',1);

/*
|--------------------------------------------------------------------------
| Constants msg level
|--------------------------------------------------------------------------
*/

define('DEBUG_LEVEL_CUSTOM',0);
define('DEBUG_LEVEL_SQL',1);
define('DEBUG_LEVEL_ROUTE',2);
define('DEBUG_LEVEL_CRITICAL',3);


/* End of file constants.php */
/* Location: ./application/config/constants.php */