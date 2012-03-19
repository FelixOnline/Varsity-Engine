<?php
    $dbname = "media_felix";
    $host = "localhost";
    $user = "root";
    $pass = "password";
    //$cid = mysql_connect($host,$user,$pass);
    //$dbok = mysql_select_db($dbname,$cid);

    /* Initialise ezSQL database connection */
    $db = new ezSQL_mysql();
    $db->quick_connect($user,$pass,$dbname,$host);

    /* Set settings for caching (turned off by defualt) */
	// Cache expiry
    $db->cache_timeout = 24; // Note: this is hours
    //$db->use_disk_cache = true;
    //$db->cache_dir = 'inc/ezsql_cache'; // Specify a cache dir. Path is taken from calling script
    $db->show_errors();

    /* Forces charset to be utf8 */
    mysql_set_charset('utf8',$db->dbh);
    //mysql_set_charset('utf8',$cid);

    /* turn off error reporting */
    //error_reporting(0);
    /* to turn on error reporting uncomment line: */
    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    /*
     * Change these urls to your local versions, e.g http://localhost/felix
     */
    define('STANDARD_SERVER', 'felixonline.local');
    define('STANDARD_URL','http://felixonline.local/');
    define('ADMIN_URL','http://localhost/felix/engine/');
	define('AUTHENTICATION_SERVER','localhost');
	define('AUTHENTICATION_PATH','http://localhost/felix/');
	//define('RELATIVE_PATH','/felix'); // relative path from root

    define('PRODUCTION_FLAG', false); // if set to true css and js will be minified etc..
    define('LOCAL', true); // if true then site is hosted locally - don't use pam_auth etc. 

    define('TIMING', true);

    define('API_KEY', 'd43bcdca90f82b5918d40c50ee20198e');
    
?>
