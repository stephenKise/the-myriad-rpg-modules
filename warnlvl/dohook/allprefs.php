<?php
	if( $session['user']['superuser'] & SU_EDIT_USERS )
	{
		$id = httpget('userid');
		addnav('General Modules');
		addnav('Warning Level',"runmodule.php?module=warnlvl&op=superuser&subop=edit&userid=$id");
	}
?>