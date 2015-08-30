<?php

define ('MUZIEKLADDER_BASE_PATH','/'.drupal_get_path('module','mod_muziekladder')); 
define ('MUZIEKLADDER_SYSTEM_PATH', str_replace('/app','', dirname(__FILE__)));
define ('MUZIEKLADDER_REQUEST_PATH',request_path()); 

if (!stristr( $_SERVER['SERVER_NAME'], 'muziekladder.nl' )){
    // Test
    $old_siteroot = '/../../../../../output';
    define ('MUZIEK_NEWSPORTAL', '/home/joriso/devlop/newscrawl/newscrawl_output/pages/3.html');
    define ('MUZIEK_SOLRHOST', 'http://localhost:8983/solr/core0/');
    
    // die on all errors includig notice  / warning
    function errHandle($errNo, $errStr, $errFile, $errLine) {
        $msg = "$errNo $errStr in $errFile on line $errLine";
        if ($errNo == E_NOTICE || $errNo == E_WARNING) {
            echo ($msg); exit;
        } else {
            echo $msg; exit;
        }
    }
    set_error_handler('errHandle');

} else {
    // Production
    $old_siteroot = '/../../../../../public';
    define ('MUZIEK_NEWSPORTAL', '/home/joriso/web/muziekladder.nl/PERLNEWS/newscrawl_output/pages/3.html'); 
    define ('MUZIEK_SOLRHOST', 'http://localhost:8983/solr/core0/');
}

define ('MUZIEK_GEODATA_JSON',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot.  '/data/geodata.json');
define ('MUZIEK_USERDATA_DIR',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot.  '/data/user');
define ('MUZIEK_GEODATA_DIR',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot.  '/data');
define ('MUZIEK_SQL_DIR',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot. '/db');

define ('MUZIEK_DATA',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/muziek'); 
define ('MUZIEK_DATA_GIGS',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/gig/gigdata.xml'); 
define ('MUZIEK_DATA_LOCATION_INDEX',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/gig/locationFileIndex.xml'); 
define ('MUZIEK_DATA_LOCATIONS',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/gig/locations.xml'); 
define ('MUZIEK_DATA_UITGAAN',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/uitgaan');

define ('MUZIEK_DATA_JSON','/muziekdata'); 
define ('MUZIEK_VIEW_DIR',MUZIEKLADDER_SYSTEM_PATH . '/app/views'); 
define ('MUZIEK_COMPONENTS',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot .'/components'); 

$includepaths = array('/app/controllers'); 
 
foreach($includepaths as $path){         
    set_include_path(get_include_path() . PATH_SEPARATOR . MUZIEKLADDER_SYSTEM_PATH . $path);       
}
require_once('singleton.php'); 
require_once('util.php'); 
require_once('db.php');
require_once('forms.php'); 

