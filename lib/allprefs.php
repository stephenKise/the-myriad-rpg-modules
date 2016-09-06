<?php
// Utility functions to handle DaveS $allprefs style user preferences
// The lib/modules.php functions do the hard work and all the caching etc.
// Daniel Kalchev aka danbi 
// 08-12-2006

// Get setting from the allprefs array
function get_module_allpref($name,$module=false,$user=false){
	$allprefs = get_module_pref("allprefs",$module,$user);
	if (isset($allprefs)) {
		// allprefs found
		$allprefs=unserialize($allprefs);
		if (isset($allprefs[$name]))
			// return value if defined
			return $allprefs[$name];
	} else {
		// allprefs not found, save an empty array in database
		set_module_pref("allprefs",serialize(array()),$module,$user);
	}
	// return null if not initialized
	return NULL;
}

function set_module_allpref($name,$value,$module=false,$user=false){
	$allprefs = get_module_pref("allprefs",$module,$user);
	if (isset($allprefs))
		$allprefs=unserialize($allprefs);
	else
		$allprefs=array();
	$allprefs[$name] = $value;
	set_module_pref("allprefs",serialize($allprefs),$module,$user);
}

function increment_module_allpref($name,$value=1,$module=false,$user=false){
	$old = get_module_allpref($name,$module,$user);
	if (!isset($old))
		$old = 0;
	set_module_allpref($name, $old+$value, $module, $user);
}

function clear_module_allpref($name,$module=false,$user=false){
	// just set the array element to NULL
	// as set_module_allpref() will then remove it
	set_module_allpref($name, NULL, $module, $user);
}

?>
