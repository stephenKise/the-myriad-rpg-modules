<?php
	global $session;
	switch ($hookname) {
		case "footer-clan":
			require_once("modules/clanvault/clanvault_dohook_footer-clan.php");
		break;
		case "newday-runonce":
			require_once("modules/clanvault/clanvault_dohook_newday-runonce.php");
       	break;
		case "newday":
			require_once("modules/clanvault/clanvault_dohook_newday.php");
       	break;
		case "dragonkill":
			require_once("modules/clanvault/clanvault_dohook_dragonkill.php");
		break;
		case "checkuserpref":
			require_once("modules/clanvault/clanvault_dohook_checkuserpref.php");
		break;
	}
	return $args;
?>