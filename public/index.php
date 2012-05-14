<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);
date_default_timezone_set('Europe/London');

$rootDir = dirname(dirname(__FILE__));
set_include_path($rootDir . '/library' . PATH_SEPARATOR . $rootDir . '/application/models/'
   . PATH_SEPARATOR . get_include_path());

include "Zend/Loader.php";
Zend_Loader::loadClass('Zend_Controller_Front');
//require_once 'Zend/Controller/Front.php';

require_once 'Zend/Config/Ini.php';
require_once 'Zend/Registry.php'; 
require_once 'Zend/Db.php';
require_once 'Zend/Db/Table.php'; 

// load configuration
$config = new Zend_Config_Ini('../application/configs/config.ini','general');
$registry = Zend_Registry::getInstance();
$registry->set('config', $config); 

// setup database 
$db = Zend_Db::factory(	$config->db->adapter,$config->db->config->toArray() );
Zend_Db_Table::setDefaultAdapter($db); 

Zend_Controller_Front::run('../application/controllers');

?>
