<?php

/**
 *	@module			Anytopics
 *	@version		see info.php of this module
 *	@authors		Chio Maisriml, Dietrich Roland Pehlke, erpe
 *	@copyright		2008-2012 Chio Maisriml
 *	@copyright		2012-2017 Dietrich Roland Pehlke, erpe
 *	@license		GNU General Public License
 *	@license terms	see info.php of this module
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('LEPTON_PATH')) {	
	include(LEPTON_PATH.'/framework/class.secure.php'); 
} else {
	$root = "../";
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= "../";
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

$module_directory     = 'anytopics';
$module_name          = 'Topics and Comments anywhere';
$module_function      = 'snippet';
$module_version       = '0.5.0';
$module_platform      = '2.x';
$module_author        = 'Chio';
$module_license       = 'GNU General Public License';
$module_description   = 'Snippet to display the last topics and comments (Topics-Module). Simplest call: display_topics();';
$module_guid		  = 'A7B24103-7C91-426C-BBB5-3AA2B6E4B574';
?>