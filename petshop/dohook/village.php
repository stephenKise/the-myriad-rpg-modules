<?php
	if( $session['user']['location'] == get_module_setting('petshoploc') )
	{
		tlschema($args['schemas']['marketnav']);
		addnav($args['marketnav']);
		tlschema();
		addnav(array('%s`0',get_module_setting('petshopname')),'runmodule.php?module=petshop&loc=village');
	}
?>