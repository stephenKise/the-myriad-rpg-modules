<?php
	if( $allprefs['haspet'] > 0 )
	{
		output('`^Pet: `@%s `@the %s`0`n', $allprefs['petname'], $allprefs['pettype']);		
	}
	else
	{
		output('`^Pet: `@None`0`n');
	}
	if( $session['user']['superuser'] & SU_EDIT_USERS )
	{
		$id = $args['acctid'];
		addnav('Superuser');
		addnav('Edit Player\'s Pet','runmodule.php?module=petshop&op=editor&subop=edit&userid='.$id);
	}
?>