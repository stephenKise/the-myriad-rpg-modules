<?php
	if( $session['user']['superuser'] & SU_EDIT_USERS )
	{
		$id = httpget('userid');
		addnav('General Modules');
		addnav('Pet Editor',"runmodule.php?module=petshop&op=editor&subop=edit&userid=$id");
	}
?>