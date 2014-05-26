<?php
define ('MUZIEKLADDER_BASE_PATH','/'.drupal_get_path('module','mod_muziekladder')); 
define ('MUZIEKLADDER_SYSTEM_PATH', str_replace('/app','', dirname(__FILE__))); 
define ('MUZIEKLADDER_REQUEST_PATH',request_path()); 

if (!stristr( $_SERVER['SERVER_NAME'], 'muziekladder.nl' )){
    // Test
	$old_siteroot = '/../../../../../output';
    define ('MUZIEK_SOLRHOST', 'http://localhost:8983/solr/core0/');
 
} else {
    // Production
	$old_siteroot = '/../../../../../public';
    define ('MUZIEK_SOLRHOST', 'http://doubleplusgood.nl/srch/muziekladderd/');
 
}

define ('MUZIEK_GEODATA_JSON',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot.  '/data/geodata.json');
define ('MUZIEK_DATA',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/muziek'); 
define ('MUZIEK_DATA_GIGS',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/gig/gigdata.xml'); 
define ('MUZIEK_DATA_LOCATION_INDEX',MUZIEKLADDER_SYSTEM_PATH . $old_siteroot . '/gig/locationFileIndex.xml'); 
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

