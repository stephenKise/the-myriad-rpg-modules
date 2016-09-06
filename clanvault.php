<?php
require_once("lib/commentary.php");
require_once("lib/villagenav.php");
require_once("lib/systemmail.php");
require_once("lib/sanitize.php");

function clanvault_getmoduleinfo() {
	require_once("modules/clanvault/clanvault_getmoduleinfo.php");
	return clanvault_private_moduleinfo();
}

function clanvault_install() {
	require_once("modules/clanvault/clanvault_install.php");
}

function clanvault_uninstall() {
	output("`n`c`b`QGuildvault Module - Uninstalled`0`b`c");
	return true;
}

function clanvault_dohook($hookname,$args) {
	require_once("modules/clanvault/clanvault_dohook.php");
	return $args;
}

function clanvault_run() {
	require_once("modules/clanvault/clanvault_run.php");
}
?>