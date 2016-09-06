<?php
	if( $args['setting'] == 'villagename' )
	{
		if( $args['old'] == get_module_setting('petshoploc') && $args['old'] != $args['new'] )
		{
			set_module_setting('petshoploc', $args['new']);
		}
	}
?>