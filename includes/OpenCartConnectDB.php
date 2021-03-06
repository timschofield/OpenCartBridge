<?php

/* $Revision: 0.01 $ */

if (!file_exists('OpenCart_config.php')){
	echo '<P>' . _("webERP - Opencart connector can't access the OpenCart_config.php file");
	include ('includes/footer.php');
} else {
	include ('OpenCart_config.php');
}

if (!isset($mysqlport)){
	$mysqlport = 3306;
}

global $db_oc;	// Make sure it IS global, regardless of our context
global $oc_tableprefix; 	// Make sure it IS global, regardless of our context

$oc_tableprefix = $opencart_db_tableprefix;
$db_oc = mysqli_connect($opencart_db_host , $opencart_db_user, $opencart_db_pwd, $opencart_db_name, $mysqlport);
mysqli_set_charset($db_oc, 'utf8');

if ( !$db_oc ) {
	prnMsg(_('The configuration in the file config.php for the OpenCart database user name, password and host do not provide the information required to connect to the OpenCart database server'),'error');
	exit;
}

function DB_query_oc($SQL, $ErrorMessage = '', $DebugMessage = '', $Transaction = false, $TrapErrors = true) {

	global $db_oc;
	global $PathPrefix;

	$Result = mysqli_query($db_oc, $SQL);

	if ($DebugMessage == '') {
		$DebugMessage = _('The SQL that failed was');
	}

	if (DB_error_no($db_oc) != 0 and $TrapErrors == true) {
		if ($TrapErrors) {
			require_once($PathPrefix . 'includes/header.inc');
		}
		prnMsg($ErrorMessage . '<br />' . DB_error_msg($db_oc), 'error', _('Database Error') . ' ' . DB_error_no($db_oc));
		if ($Debug == 1) {
			prnMsg($DebugMessage . '<br />' . $SQL . '<br />', 'error', _('Database SQL Failure'));
		}
		if ($Transaction) {
			$SQL = 'rollback';
			$Result = DB_query_oc($SQL);
			if (DB_error_no() != 0) {
				prnMsg(_('Error Rolling Back Transaction'), 'error', _('Database Rollback Error') . ' ' . DB_error_no($db_oc));
			} else {
				prnMsg(_('Rolling Back Transaction OK'), 'error', _('Database Rollback Due to Error Above'));
			}
		}
		if ($TrapErrors) {
			include($PathPrefix . 'includes/footer.inc');
			exit;
		}
	}

	return $Result;

}

?>
