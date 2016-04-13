<?php 
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/..' )); /* now MRBS is in a subfolder from Joomla. Check the manual on http://php.net/manual/en/function.realpath.php */
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe	=& JFactory::getApplication('site');
$user		=& JFactory::getUser();
//Database
$dbsys =		$mainframe->getCfg('dbtype');	// Database driver name
$db_host =		$mainframe->getCfg('host');		// Database host name
$db_database =	$mainframe->getCfg('db');		// Database name
$db_login =		$mainframe->getCfg('user');		// User for database authentication
$db_password =	$mainframe->getCfg('password');	// Password for database authentication
$dbprefix  =	$mainframe->getCfg('dbprefix');
//User
$usergid =		max ($user->getAuthorisedGroups());
setlocale(LC_MONETARY, 'nl_NL');
// Default language joomla
$frontendsiteDefaultLanguage = JComponentHelper::getParams('com_languages')->get('site'); 
$lang_code_partials = explode("-", $frontendsiteDefaultLanguage);
$lang_prefix = array_shift($lang_code_partials);


$db = array ( 
    'host' => $db_host, 
    'user' => $db_login, 
    'pass' => $db_password, 
    'dbname' => $db_database 
); 


if(!mysql_connect($db['host'], $db['user'], $db['pass'])) 
{ 
    trigger_error('Fout bij verbinden: '.mysql_error()); 
} 
elseif(!mysql_select_db($db['dbname'])) 
{ 
    trigger_error('Fout bij selecteren database: '.mysql_error()); 
} 
else 
{ 
    $sql = "SET SESSION sql_mode = 'ANSI,ONLY_FULL_GROUP_BY'"; 
    if(!mysql_query($sql)) 
    { 
        trigger_error('MySQL in ANSI niet mogelijk'); 
    } 
} 


function authGetUserLevel($user)
{
  global $usergid,$min_manager_grouplevel;

  // User not logged in, user level '0'
  if(!isset($user))
  {
    return 0;
  }

  // Check if the user is can modify
  if($usergid > 2)
  {
    return 2;
  }


  // Everybody else is access level '1'
  return 1;
}

?>
