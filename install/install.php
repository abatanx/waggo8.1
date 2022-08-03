<?php
/**
 * waggo8.1
 * @copyright 2013-2022 CIEL, K.K., project waggo.
 * @license MIT
 */

const WG_INSTALL_VERSION = '8.1';

require_once __DIR__ . '/lib/stdin.php';
require_once __DIR__ . '/lib/lib.php';

const STATE_ABORT        = - 1;
const STATE_END          = 0;
const STATE_LICENSE      = 1;
const STATE_DIR_CHECK    = 2;
const STATE_DIR          = 3;
const STATE_INSTALL_INFO = 4;

$state = STATE_LICENSE;

$framework = wi_detect_waggo_version();

for ( $isTerminate = false; ! $isTerminate; )
{
	wi_cls();
	echo <<<___END___
--------------------------------------------------------------------------------

\t{$framework["name"]} version {$framework["version"]} installer
\t{$framework["copyright"]}

--------------------------------------------------------------------------------

___END___;

	switch ( $state )
	{
		case STATE_LICENSE:
			wi_echo( ECHO_SPACING, 'License' );
			require_once __DIR__ . '/lib/license.php';
			$state = wi_license_agreement() ? STATE_DIR_CHECK : STATE_ABORT;
			break;

		case STATE_DIR_CHECK:
			wi_echo( ECHO_SPACING, 'Directory Check' );
			require_once __DIR__ . '/lib/check_directory.php';
			$state = wi_setup_dir() ? STATE_DIR : STATE_ABORT;
			break;

		case STATE_DIR:
			wi_echo( ECHO_SPACING, 'Directory operation' );
			require_once __DIR__ . '/lib/check_directory.php';
			$state = wi_setup_dir_and_permissions() ? STATE_INSTALL_INFO : STATE_ABORT;
			break;

		case STATE_INSTALL_INFO:
			wi_echo( ECHO_SPACING, 'Setup' );
			require_once __DIR__ . '/lib/install_information.php';
			$state = wi_install() ? STATE_END : STATE_INSTALL_INFO;
			break;

		default:
			wi_echo( ECHO_SPACING, '... Errors in setup ...' );
			$state = STATE_ABORT;
			break;
	}

	switch ( $state )
	{
		case STATE_ABORT:
			wi_echo( ECHO_NORMAL, 'Aborted.' );
			$isTerminate = true;
			break;

		case STATE_END:
			wi_echo( ECHO_NORMAL, 'Done.' );
			$isTerminate = true;
			break;
	}
}
