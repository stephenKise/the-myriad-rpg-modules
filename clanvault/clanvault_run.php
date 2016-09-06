<?php
	global $session;
	page_header("Guild Vault");
	require_once("modules/clanvault/clanvault_damnbloat.php");
	switch ($op) {
		case "enter":
			require_once("modules/clanvault/clanvault_run_enter.php");
		break;
		case "cancel":
			require_once("modules/clanvault/clanvault_run_cancel.php");
		break;
		case "deposit":
			require_once("modules/clanvault/clanvault_run_deposit.php");
		break;
		case "withdraw":
			require_once("modules/clanvault/clanvault_run_withdraw.php");
		break;
		case "stipend":
			require_once("modules/clanvault/clanvault_run_stipend.php");
		break;
		case "donate":
			require_once("modules/clanvault/clanvault_run_donate.php");
		break;
		case "preference":
			require_once("modules/clanvault/clanvault_run_preference.php");
		break;
		case "request":
			require_once("modules/clanvault/clanvault_run_request.php");
		break;
		default:
			output("Shouldn't be here.");
		break;
	}
	page_footer();
?>