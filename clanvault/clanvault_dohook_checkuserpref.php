<?php
	if (!$session['user']['clanid'] || ($session['user']['clanid'] && $session['user']['clanrank'] <= 20))
		$args['allow'] = false;
?>