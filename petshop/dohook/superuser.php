<?php
	if( $session['user']['superuser'] & SU_EDIT_MOUNTS )
	{
		addnav('Editors');
		addnav('Pet Editor','runmodule.php?module=petshop&op=editor&op2=view');
	}
?>