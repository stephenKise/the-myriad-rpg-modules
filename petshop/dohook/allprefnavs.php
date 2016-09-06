<?php
	$id = httpget('userid');
	addnav('General Modules');
	addnav('Pet Editor',"runmodule.php?module=petshop&op=editor&subop=edit&userid=$id");
?>